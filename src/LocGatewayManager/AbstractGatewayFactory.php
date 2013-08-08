<?php
namespace LocGatewayManager;

use Zend\ServiceManager;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Db\Adapter\Adapter;

abstract class AbstractGatewayFactory implements ServiceManager\ServiceLocatorAwareInterface
{
    /**
     * @var string Name to look for in configurations
     */
    const CONFIG_LOC_DB = 'loc_gateway_manager';

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
    public function createAdapter($adapterKeyName = null)
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
                if (isset($db['default'])) { // look for defaults
                    $db = is_array($db['default']) ? $db['default'] : null;
                } else {
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
    public function createEntity($entityName, Db\Adapter $adapter)
    {
        $config = $this->getServiceLocator()->get('config');
        $class = $config[self::CONFIG_LOC_DB]['entities'] . "\\" .  $entityName;

        if (!class_exists($class)) {
            $class = $class . 'Entity';
            if (!class_exists($class)) {
                throw new Exception\ClassNotExistException('Entity ' . $class . ' does not exist.');
            }
        }

        return new $class();
    }

    /**
     * @param string $featureName
     * @throws Exception\ClassNotExistException
     * @return Feature\AbstractFeature|null
     */
    public function createFeature($featureName = null)
    {
        if (isset($featureName)) {
            $wordfilter = new UnderscoreToCamelCase();
            $featureName = $wordfilter->filter($featureName);
            $class = __NAMESPACE__ . "\\Feature\\{$featureName}";

            if (!class_exists($class)) {
                throw new Exception\ClassNotExistException('Class: ' . $class . ', does not exist.');
            }

            return new $class();
        }
    }

    /**
     * @param string $resultSetName
     * @throws Exception\ClassNotExistException
     * @return ResultSet\AbstractResultSet|null
     */
    public function createResultSet($resultSetName = null)
    {
        if (isset($resultSetName)) {
            $wordfilter = new UnderscoreToCamelCase();
            $resultSetName = $wordfilter->filter($resultSetName);
            $class = __NAMESPACE__ . "\\ResultSet\\{$resultSetName}";

            if (!class_exists($class)) {
                throw new Exception\ClassNotExistException('Class ' . $class . ' does not exist.');
            }

            return new $class();
        }
    }

    /**
     * @param string $tableName
     * @param string $default
     * @throws Exception\ClassNotExistException
     * @return string
     */
    public function createTable($tableName = null, $default = null)
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
                if (!$table instanceof Gateway\TableInterface) {
                    return $table->getTableName();
                } elseif (!empty($table->tableName)) {
                    return $this->tableName;
                }
            }
        }

        if (is_object($default)) {
            return $this->normalizeTablename(get_class($default));
        }

        // if nothing still just return tableName
        if (is_string($tableName)) {
            return $tableName;
        }
    }

    /**
     * Normalize a table name
     * @param string $tablename
     * @return Ambigous <string, string, mixed>
     */
    protected function normalizeTablename($tablename)
    {
        $wordFilter = new Word\CamelCaseToUnderscore;
        $tableArray = explode('_', $wordFilter->filter($tablename));
        $lastWord = strtolower($tableArray[(count($tableArray) - 1)]);
        if (in_array($lastWord, array('entity', 'table'))) {
            array_pop($tableArray);
        }
        $tablename = implode('_', $tableArray);
        $wordFilter = new Word\UnderscoreToCamelCase;
        return ucfirst($wordFilter->filter($tablename));
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
