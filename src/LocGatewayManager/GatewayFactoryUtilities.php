<?php
namespace LocGatewayManager;

use Zend\Db\Adapter\Adapter;
use Zend\Filter\Word;
use Zend\ServiceManager;

class GatewayFactoryUtilities implements ServiceManager\ServiceLocatorAwareInterface
{
    /**
     * @var string Name to look for in configurations
     */
    const CONFIG_LOC_DB = 'loc_gateway_manager_assets';

    /**
     * @var string
     */
    protected $adapterKey;

    /**
     * @var ServiceManager\ServiceManager
     */
    protected $serviceLocator;

    /**
     * Retrieve the db adapter
     *
     * @param string $adapterKeyName
     * @throws \ErrorException
     * @return \Zend\Db\Adapter\Adapter
     */
    public function initAdapter($adapterKeyName = null)
    {
        $config = $this->getServiceLocator()->get('config');
        switch (true) {
            case isset($config['database']):
                $db = $config['database'];
                break;
            case isset($config['db']):
                $db = $config['db'];
                break;
        }

        if (!isset($db)) {
            throw new Exception\InvalidArgumentException('Adapter config is not found.');
        }

        $adapterKeyName = $adapterKeyName ?: $this->getAdapterKey();
        if (!empty($adapterKeyName)) {
            if (is_array($db[$adapterKeyName])) {
                $db = $db[$adapterKeyName];
            }
        } else {
            if (is_array($db)) {
                if (isset($db['default'])) { // look for default key
                    $db = is_array($db['default']) ? $db['default'] : null;
                } else { // just get the input in the config
                    $firstConfig = array_shift($db);
                    if (is_array($firstConfig)) {
                        $db = $firstConfig;
                    }
                }
            }
        }

        return new Adapter($db);
    }

    /**
     * Initialize a Table entity
     * @param string $entityName Entity name
     * @throws \ErrorException
     * @return Object
     */
    public function initEntity($entityName)
    {
        // if fqns return it
        if (class_exists($entityName)) {
            $class = $entityName;
        } else {
            $class = $this->getFQNSClass($entityName, 'entity');
            if (null === $class) {
                throw new Exception\ClassNotExistException('Entity ' . $entityName . ' does not exist.');
            }
        }

        return new $class();
    }

    /**
     * @param string $featureName
     * @throws Exception\ClassNotExistException
     * @return Feature\AbstractFeature|null
     */
    public function initFeature($featureName = null)
    {
        if (isset($featureName)) {
            if (class_exists($featureName)) {
                $class = $featureName;
            } else {
                $wordfilter = new Word\UnderscoreToCamelCase();
                $featureName = $wordfilter->filter($featureName);
                $class = __NAMESPACE__ . "\\Feature\\{$featureName}";
                if (!class_exists($class)) {
                    throw new Exception\ClassNotExistException('Class: ' . $class . ', does not exist.');
                }
            }
            return new $class();
        }
    }

    /**
     * @param string $resultSetName
     * @throws Exception\ClassNotExistException
     * @return ResultSet\AbstractResultSet|null
     */
    public function initResultSet($resultSetName = null)
    {
        if (isset($resultSetName)) {
            if (class_exists($resultSetName)) {
                $class = $resultSetName;
            } else {
                $wordfilter = new Word\UnderscoreToCamelCase();
                $resultSetName = $wordfilter->filter($resultSetName);
                $class = __NAMESPACE__ . "\\ResultSet\\{$resultSetName}";
                if (!class_exists($class)) {
                    throw new Exception\ClassNotExistException('Class ' . $class . ' does not exist.');
                }
            }
            return new $class();
        }
    }

    /**
     * Get the adapter key
     * @return string
     */
    public function getAdapterKey()
    {
        return $this->adapterKey;
    }

    /**
     * Se the adapter key
     * @param string $key
     * @return \LocGatewayManager\AbstractGatewayFactory
     */
    public function setAdapterKey($key)
    {
        $this->adapterKey = $key;
        return $this;
    }

    /**
     * @param string $tableName
     * @param string $fallback
     * @param Zend\Db\Adapter\Adapter
     * @throws Exception\ClassNotExistException
     * @return string
     */
    public function getTable($tableName = null, $fallback = null, Adapter $adapter = null)
    {
        if (class_exists($tableName)) {
            $class = $tableName;
        } else {
            $class = $this->getFQNSClass($tableName, 'table');
        }

        if ($class !== null) {
            $table = new $class();
            if ($table instanceof Gateway\TableInterface && $table->getTableName() !== null) {
                return $table->getTableName();
            } elseif (!empty($table->tableName)) {
                return $this->tableName;
            } else {
                $class = $this->extractClassnameFromNamespace($class);
                return $this->normalizeTablename($class, $adapter);
            }
        }

        if (is_object($fallback)) {
            // check first on a table class. do a loop back
            $class = $this->getTableGatewayTarget(null, $fallback);
            $class = $this->getTable($class, null, $adapter);

            if (isset($class)) {
                return $class;
            } else {
                // lastly check on the entity object if a tableName is declared
                if (property_exists($fallback, 'tableName')) {
                    return $fallback->tableName;
                } else {
                    $fallback = $this->extractClassnameFromNamespace($fallback);
                    return $this->normalizeTablename($fallback, $adapter);
                }
            }
        }

        throw new Exception\InvalidArgumentException('Cannot identify tablename.');
    }

    /**
     * Create the table gateway class to use
     * @param string $tableGatewayName
     * @param object $fallback
     * @return string
     */
    public function getTableGatewayTarget($tableGatewayName = null, $fallback = null)
    {
        $classString = $this->getFQNSClass($tableGatewayName, 'table');
        if (!isset($classString) && isset($fallback)) {
            $fallback = $this->extractClassnameFromNamespace($fallback);
            $classString = $this->getFQNSClass($fallback, 'table');
        }
        return $classString;
    }

    /**
     * @return string Gateway name from config
     */
    public function getConfigGatewayName()
    {
        $config = $this->getServiceLocator()->get('config');
        if (!empty($config[self::CONFIG_LOC_DB]['gateway'])) {
            $gatewayName = $config[self::CONFIG_LOC_DB]['gateway'];
            if (!empty($config['loc_gateway_manager_gateways'][$gatewayName])) {
                $gateway = $config['loc_gateway_manager_gateways'][$gatewayName];
            }
        } else {
            $gateway = $config['loc_gateway_manager_gateways']['default'];
        }
        return $gateway;
    }

    /**
     * Retrieve the fully qualified class namespace
     * @param string $className
     * @param string $type
     * @return string|null
     */
    public function getFQNSClass($className, $type)
    {
        $typeMapping = array(
            'table' => 'tables',
            'entity' => 'entities',
        );

        $class = null;
        if (array_key_exists($type, $typeMapping)) {
            $config = $this->getServiceLocator()->get('config');
            $config = $config[self::CONFIG_LOC_DB];

            $adapterKey = $this->getAdapterKey() ?: 'default';
            foreach ($config as $key => $val) {
                if (is_array($val)) {
                    if ($key == $adapterKey) {
                        $settingKey = $typeMapping[$type];
                        $class = "{$val[$settingKey]}\\{$className}";
                        break;
                    }
                } else {
                    if ($key == $typeMapping[$type]) {
                        $class = "{$val}\\{$className}";
                        break;
                    }
                }
            }

            if (isset($class)) {
                if (!class_exists($class)) {
                    $class = $class . ucfirst($type);
                    if (!class_exists($class)) {
                        $class = null;
                    }
                }
            }
        }

        return $class;
    }

    /**
     * Normalize a tablename
     * @param string $tablename
     * @param Zend\Db\Adapter\Adapter
     * @return Ambigous <string, string, mixed>
     */
    protected function normalizeTablename($tablename, $adapter = null)
    {
        $wordFilter = new Word\CamelCaseToUnderscore;
        $tableArray = explode('_', $wordFilter->filter($tablename));
        $lastWord = strtolower($tableArray[(count($tableArray) - 1)]);
        if (in_array($lastWord, array('entity', 'table'))) {
            array_pop($tableArray);
        }
        $tablename = implode('_', $tableArray);
        $namespacePos = strrpos($tablename, "\\");
        if ($namespacePos > 0) {
            $tablename = substr($tablename, $namespacePos + 1);
        }

        // check if oracle driver
        if (isset($adapter) && $adapter->getDriver()->getDatabasePlatformName() === 'Oracle') {
            $tablename = strtoupper($tablename);
        }

        return $tablename;
    }

    /**
     * @param string|object $namespace
     * @return mixed
     */
    protected function extractClassnameFromNamespace($namespace)
    {
        if (is_object($namespace)) {
            $namespace = get_class($namespace);
        }

        if (is_string($namespace)) {
            $namespace = explode("\\", $namespace);
            $namespace = array_pop($namespace);
            return $namespace;
        }
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        if (null === $this->serviceLocator) {
            throw new Exception\ClassNotExistException('Service Locator is not set');
        }
        return $this->serviceLocator;
    }

    /**
     * Set service locator
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}
