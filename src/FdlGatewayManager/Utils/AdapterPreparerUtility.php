<?php
namespace FdlGatewayManager\Utils;

use FdlGatewayManager\AbstractServiceLocatorAware;
use FdlGatewayManager\Exception;
use Zend\Soap\call_user_func;

class AdapterPreparerUtility extends AbstractServiceLocatorAware implements PreparerUtilityInterface
{
    /**
     * @var Array
     */
    protected $dbConfig;

    /**
     * The seed
     * @var string
     */
    protected $adapterKey;

    /**
     * (non-PHPdoc)
     * @see \FdlGatewayManager\Utils\PreparerUtilityInterface::seed()
     */
    public function seed($seed = null)
    {
        $this->adapterKey = $seed;
    }

    /**
     * (non-PHPdoc)
     * @see \FdlGatewayManager\Utils\PreparerUtilityInterface::destroySeed()
     */
    public function destroySeed()
    {
        $this->adapterKey = null;
    }

    /**
     * Return the Adapter Class Object options
     *
     * Since this will return the Adapter object, it will override
     * the other options in this param
     *
     * @return \Zend\Db\Adapter\Adapter|mixed
     */
    public function getAdapterClass()
    {
        $optionsConfig = $this->getOptionsConfig();
        $adapterClass = isset($optionsConfig['adapter_class']) ? $optionsConfig['adapter_class']
                      : (isset($optionsConfig['adapter']) ? $optionsConfig['adapter'] : null);

        if (null !== $adapterClass) {
            return $this->executeClass($adapterClass);
        }

        return $adapterClass;
    }

    /**
     * @param array $config
     * @return array
     */
    public function getOptionDriverParams()
    {
        $driverParams = $this->getAdapterConfig();

        if (isset($driverParams['options'])) {
            unset($driverParams['options']);
        }

        return $driverParams;
    }

    public function getOptionPlatform()
    {
        $optionsConfig = $this->getOptionsConfig();

        if (isset($optionsConfig['platform'])) {
            $namespace = 'Zend\Db\Adapter\Platform';
            $errorMsg  = 'Platform for db driver does not exist';
            return $this->executeClass($optionsConfig['platform'], $namespace, $errorMsg);
        }
    }

    public function getOptionQueryResultPrototype()
    {
        $optionsConfig = $this->getOptionsConfig();

        if (isset($optionsConfig['query_result_prototype'])) {
            $namespace = 'Zend\Db\ResultSet';
            $errorMsg  = 'Query Result Prototype for db driver does not exist.';
            return $this->executeClass($optionsConfig['query_result_prototype'], $namespace, $errorMsg);
        }
    }

    public function getOptionProfiler()
    {
        $optionsConfig = $this->getOptionsConfig();

        if (isset($optionsConfig['profiler'])) {
            $namespace = 'Zend\Db\Adapter\Profiler';
            $errorMsg  = 'Query Result Prototype for db driver does not exist.';
            return $this->executeClass($optionsConfig['profiler'], $namespace, $errorMsg);
        }
    }

    /**
     * Retrieve the adapter key
     * Lazy loads the adapter key from
     * @return string
     */
    public function getAdapterKey()
    {
        return $this->adapterKey;
    }

    /**
     * Return the options param from the dbConfig
     * @return array|null
     */
    public function getOptionsConfig()
    {
        $adapterConfig = $this->getAdapterConfig();
        if (isset($adapterConfig['options'])) {
            return $adapterConfig['options'];
        }
    }

    /**
     * Retrieve the adapter config depending on the adapterKey
     *
     * 1.) Adapter key exist, retrieve that array
     * 2.) No adapter key but 'default' key exist, return it
     * 3.) No adapter, no 'default', return the first array instance
     * 4.) If non of the above will return itself, if itself is an array
     *
     * Example: with and adapter key of 'mysql'
     *
     * <code>
     *     'mysql' => array(
     *          'driver'   => '',
     *          'dsn'      => '',
     *          'username' => '',
     *          'password' => '',
     *          'options'  => array(),
     *     ),
     * </code>
     *
     * @throws Exception\ErrorException
     * @return unknown
     */
    public function getAdapterConfig()
    {
        $dbConfig = $this->getDbConfig();
        $adapterKeyName = $this->getAdapterKey();

        if (!empty($adapterKeyName)) {
            if (is_array($dbConfig[$adapterKeyName])) {
                $adapterConfig = $dbConfig[$adapterKeyName];
            }
        } else {
            if (is_array($dbConfig)) {
                if (isset($dbConfig['default'])) { // look for default key
                    if (is_array($dbConfig['default'])) {
                        $adapterConfig = $dbConfig['default'];
                    }
                } else { // just get the input in the config
                    $dbConfigCopy = $dbConfig;
                    $firstConfig  = array_shift($dbConfig);
                    if (is_array($firstConfig)) {
                        $adapterConfig = $firstConfig;
                    } else {
                        // if first config is not an array just return the config
                        $adapterConfig = $dbConfigCopy;
                        unset($dbConfig, $firstConfig);
                    }
                }
            }
        }

        if (!isset($adapterConfig)) {
            throw new Exception\ErrorException('Cannot find any db driver config.');
        }

        return $adapterConfig;
    }

    /**
     * Get the database configuration from config
     *
     * Will return something the first key and its values
     *
     * <code>
     *     array(
     *         'fdl_db'       => array(),
     *         'fdl_database' => array(),
     *         'db'           => array(),
     *         'database'     => array(),
     *     )
     * </code>
     *
     * @throws Exception\InvalidArgumentException
     * @return multitype:
     */
    public function getDbConfig()
    {
        if (null === $this->dbConfig) {
            $config = $this->getServiceLocator()->get('config');
            switch (true) {
                case isset($config['fdl_db']):
                    $this->dbConfig = $config['fdl_db'];
                    break;
                case isset($config['fdl_database']):
                    $this->dbConfig = $config['fdl_database'];
                    break;
                case isset($config['db']):
                    $this->dbConfig = $config['db'];
                    break;
                case isset($config['database']):
                    $this->dbConfig = $config['database'];
                    break;
            }

            if (!isset($this->dbConfig)) {
                throw new Exception\InvalidArgumentException('Adapter config is not found.');
            }
        }

        return $this->dbConfig;
    }

    /**
     * Common execution process to check if argument <$class> is a an object
     *
     * 1. Checks if $class is a ServiceManager element
     * 2. If above not pass, check if <$class> is valid FQNS
     * 3. If above not pass, check if <$class> against arg $namespace
     * 4. If above not pass, check if <$class> is callable
     * 5. If above not pass, check if <$class> is object
     * 6. Returns an error none of above is valid
     *
     * @param mixed  $class           Target to execute in the flow
     * @param string $namespace       Namespace of the class
     * @param string $errorMessage    Error Message to produce
     * @throws Exception\ClassNotExistException
     * @return object
     */
    protected function executeClass($class, $namespace = null, $errorMessage = '')
    {
        if ((is_array($class) || is_string($class))
            && $this->getServiceLocator()->has($class)
        ) {
            return $this->getServiceLocator()->get($class);
        } elseif (is_string($class) && class_exists($class)) {
            return new $class();
        } elseif (null !== $namespace
            && is_string($class)
            && class_exists($class = "{$namespace}\\{$class}")
        ) {
            return new $class();
        } elseif (is_callable($class)) {
            return call_user_func($class);
        } elseif (is_object($class)) {
            return $class;
        } else {
            throw new Exception\ClassNotExistException($errorMessage);
        }
    }
}
