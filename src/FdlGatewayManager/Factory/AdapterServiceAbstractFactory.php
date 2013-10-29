<?php
namespace FdlGatewayManager\Factory;

use FdlGatewayManager\Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager;

class AdapterServiceAbstractFactory implements ServiceManager\AbstractFactoryInterface
{
    protected $serviceLocator;
    protected $dbConfig;

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('config');
        $serviceName = $config['fdl_gateway_manager_config']['table_gateway']['adapter'];

        return ($requestedName === $serviceName);
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $this->serviceLocator = $serviceLocator;

        $config  = $serviceLocator->get('config');
        $adapter = $config['fdl_gateway_manager_config']['adapter'];

        try {
            $adapter = $serviceLocator->get($adapter);
        } catch (\Exception $e) {
            // if no direct implementation of \Zend\Db\Adapter\Adapter
            $adapterUtilities = $serviceLocator->get('FdlAdapterServiceUtilities');
            $adapterUtilities->setAdapterKey($serviceLocator->get('FdlGatewayFactory')->getWorkerEvent()->getAdapterKey());

            if (!class_exists($adapter)) {
                throw new Exception\ErrorException('Adapter class: "' . $adapter . '" does not exist');
            }

            $adapter = new $adapter(
                $adapterUtilities->getDriverParams(),
                $adapterUtilities->getPlatform(),
                $adapterUtilities->getQueryResultPrototype(),
                $adapterUtilities->getProfiler()
            );

            if (!$adapter instanceof AdapterInterface) {
                throw new Exception\ErrorException('Adapter class: "' . $adapter . '" is not of AdapterInterface');
            }
        }

        return $adapter;
    }
}
