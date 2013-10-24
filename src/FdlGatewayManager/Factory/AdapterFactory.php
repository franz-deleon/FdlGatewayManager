<?php
namespace FdlGatewayManager\Factory;

use FdlGatewayManager\Exception;
use Zend\ServiceManager;
use Zend\Db;

class AdapterFactory implements ServiceManager\FactoryInterface
{
    protected $serviceLocator;
    protected $adapterKey;
    protected $adapterConfig;
    protected $dbConfig;

    public function createService(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->setServiceLocator($serviceLocator);
        return $this;
    }

    public function createAdapter()
    {
        $config  = $this->getServiceLocator()->get('config');
        $adapter = $config['fdl_gateway_manager']['adapter'];

        return new $adapter(
            $this->getDriverParams(),
            $this->getPlatform(),
            $this->getQueryResultPrototype(),
            $this->getProfiler()
        );
    }

    /**
     * @param array $config
     * @return array
     */
    public function getDriverParams()
    {
        $driverParams = $this->getAdapterConfig();

        if (isset($driverParams['platform'])) {
            unset($driverParams['platform']);
        }
        if (isset($driverParams['query_result_prototype'])) {
            unset($driverParams['query_result_prototype']);
        }
        if (isset($driverParams['profiler'])) {
            unset($driverParams['profiler']);
        }

        return $driverParams;
    }

    public function getPlatform()
    {
        $adapterConfig = $this->getAdapterConfig();

        $platform = null;
        if (isset($adapterConfig['platform'])) {
            $platform = $adapterConfig['platform'];
            if (class_exists($platform)) {
                $platform = new $platform();
            } else {
                $platform = "Zend\\Db\\Adapter\\Platform\\{$platform}";
                if (class_exists($platform)) {
                    $platform = new $platform();
                } else {
                    throw new Exception\ClassNotExistException('Platform for db driver does not exist.');
                }
            }
        }

        return $platform;
    }

    public function getQueryResultPrototype()
    {
        $adapterConfig = $this->getAdapterConfig();

        $qrPrototype = null;
        if (isset($adapterConfig['query_result_prototype'])) {
            $qrPrototype = $adapterConfig['query_result_prototype'];
            if (class_exists($qrPrototype)) {
                $qrPrototype = new $qrPrototype();
            } else {
                $qrPrototype = "Zend\\Db\\ResultSet\\{$qrPrototype}";
                if (class_exists($qrPrototype)) {
                    $qrPrototype = new $qrPrototype();
                } else {
                    throw new Exception\ClassNotExistException('Query Result Prototype for db driver does not exist.');
                }
            }
        }

        return $qrPrototype;
    }

    public function getProfiler()
    {
        $adapterConfig = $this->getAdapterConfig();

        $profiler = null;
        if (isset($adapterConfig['profiler'])) {
            $profiler = $adapterConfig['profiler'];
            if (class_exists($profiler)) {
                $profiler = new $profiler();
            } else {
                $profiler = "Zend\\Db\\Adapter\\Profiler\\{$profiler}";
                if (class_exists($profiler)) {
                    $profiler = new $profiler();
                } else {
                    throw new Exception\ClassNotExistException('Query Result Prototype for db driver does not exist.');
                }
            }
        }

        return $profiler;
    }

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
                    } else {
                        throw new Exception\ErrorException('Cannot find any db driver config.');
                    }
                } else { // just get the input in the config
                    $firstConfig = array_shift($dbConfig);
                    if (is_array($firstConfig)) {
                        $adapterConfig = $firstConfig;
                    } else {
                        throw new Exception\ErrorException('Cannot find any db driver config.');
                    }
                }
            }
        }

        return $adapterConfig;
    }

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
                case isset($config['database']):
                    $this->dbConfig = $config['database'];
                    break;
                case isset($config['db']):
                    $this->dbConfig = $config['db'];
                    break;
            }

            if (!isset($this->dbConfig)) {
                throw new Exception\InvalidArgumentException('Adapter config is not found.');
            }
        }

        return $this->dbConfig;
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

    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
