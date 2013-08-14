<?php
namespace LocGatewayManager;

use Zend\ServiceManager;
use Zend\Filter\Word;
use Zend\Db\Adapter\Adapter;

abstract class AbstractGatewayFactory implements ServiceManager\ServiceLocatorAwareInterface
{
    /**
     * @var string Name to look for in configurations
     */
    const CONFIG_LOC_DB = 'loc_gateway_manager_assets';

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
    public function initEntity($entityName, Adapter $adapter)
    {
        // if fqns return it
        if (class_exists($entityName)) {
            $class = $entityName;
        } else {
            $config = $this->getServiceLocator()->get('config');
            $class = $config[self::CONFIG_LOC_DB]['entities'] . "\\" .  $entityName;
            if (!class_exists($class)) {
                $class = $class . 'Entity';
                if (!class_exists($class)) {
                    throw new Exception\ClassNotExistException('Entity ' . $class . ' does not exist.');
                }
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
     * @param string $tableName
     * @param string $entity
     * @param Zend\Db\Adapter\Adapter
     * @throws Exception\ClassNotExistException
     * @return string
     */
    public function initTable($tableName = null, $entity = null, Adapter $adapter = null)
    {
        if (isset($tableName)) {
            $config = $this->getServiceLocator()->get('config');
            $class = $config[self::CONFIG_LOC_DB]['tables'] . "\\" .  $tableName;

            if (!class_exists($class)) {
                $class = $class . 'Table';
                if (!class_exists($class)) {
                    $class = null;
                }
            }

            // if no class then use the default entity classname
            if ($class !== null) {
                $table = new $class();
                if ($table instanceof Gateway\TableInterface) {
                    return $table->getTableName();
                } elseif (!empty($table->tableName)) {
                    return $this->tableName;
                }
            }
        } elseif (is_object($entity)) {
            // check first if there is a $tableName property in entity obj
            if (property_exists($entity, 'tableName')) {
                return $entity->tableName;
            } else {
                return $this->normalizeTablename(get_class($entity), $adapter);
            }
        } elseif (is_string($tableName)) {
            return $tableName;
        }

        throw new Exception\InvalidArgumentException('Cannot identify tablename.');
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
