<?php
namespace FdlGatewayManager;

use Zend\Db\Adapter\Adapter;
use Zend\Filter\Word;

class GatewayFactoryUtilities extends AbstractServiceLocatorAware
{
    /**
     * @var string
     */
    protected $adapterKey;

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
            $class = $this->getFQNSClassFromMapping($entityName, 'entities');
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
     * @return \FdlGatewayManager\AbstractGatewayFactory
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
            $class = $this->getFQNSClassFromMapping($tableName, 'tables');
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
            $class = $this->getTableGatewayProxy(null, $fallback);
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
    }

    /**
     * Create the table gateway class to use
     * @param string $tableGatewayName
     * @param object $fallback
     * @return string
     */
    public function getTableGatewayProxy($tableGatewayName = null, $fallback = null)
    {
        $classString = $this->getFQNSClassFromMapping($tableGatewayName, 'tables');
        if (!isset($classString) && isset($fallback)) {
            $fallback = $this->extractClassnameFromNamespace($fallback);
            $classString = $this->getFQNSClassFromMapping($fallback, 'tables');
        }
        return $classString;
    }

    /**
     * @return string Gateway name from config
     */
    public function getConfigGatewayName()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['fdl_gateway_manager']['gateway'];
    }

    /**
     * Retrieve the fully qualified class namespace
     * @param string $class
     * @param string $type
     * @return string|null
     */
    protected function getFQNSClassFromMapping($class, $type)
    {
        $typeMapping = array(
            'table' => 'tables',
            'entity' => 'entities',
        );

        $className = null;
        $config = $this->getServiceLocator()->get('config');
        $adapterKey = $this->getAdapterKey() ?: 'default';

        if (in_array($type, $typeMapping)) {
            $classNs = $config['fdl_gateway_manager']['asset_location'][$adapterKey][$type];
            if (isset($classNs)) {
                $className  = "{$classNs}\\{$class}";
                if (!class_exists($className)) {
                    // look for classes that hass appending Table or Entity like UserEntity
                    $typeMapping = array_flip($typeMapping);
                    $className = $className . ucfirst($typeMapping[$type]);
                    if (!class_exists($className)) {
                        $className = null;
                    }
                }
            }
        }

        return $className;
    }

    public static function normalizeTablename($tableName)
    {
        $wordFilter = new \Zend\Filter\Word\CamelCaseToUnderscore;

        $tableName = preg_replace('~[^a-z0-9]~i', '_', $tableName);
        $tableName = $wordFilter->filter($tableName);
        $tableName = explode('_', $tableName);

        // check if the table name is appended with 'table' or 'entity'
        $lastWord = strtolower($tableName[(count($tableName) - 1)]);
        if ('table' === $lastWord || 'entity' === $lastWord) {
            array_pop($tableName);
        }

        $tableName = implode('_', $tableName);

        return $tableName;
    }

    /**
     * Extract the class name from a fully qualified namespace
     * @param string|object $class
     * @return mixed
     */
    public static function extractClassnameFromFQNS($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (is_string($class)) {
            $class = substr($class, (strrpos($class, '\\') + 1));
        }

        return $class;
    }
}
