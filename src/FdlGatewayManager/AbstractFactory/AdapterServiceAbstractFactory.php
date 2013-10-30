<?php
namespace FdlGatewayManager\AbstractFactory;

use FdlGatewayManager\Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager;

class AdapterServiceAbstractFactory implements ServiceManager\AbstractFactoryInterface
{
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
        $config  = $serviceLocator->get('config');
        $adapter = $config['fdl_gateway_manager_config']['adapter'];
        $adapterUtilities = $serviceLocator->get('FdlAdapterServiceUtilities');

        try {
            $adapter = $adapterUtilities->getOptionsAdapterClass() ?: $serviceLocator->get($adapter);
        } catch (\Exception $e) {
            if (!class_exists($adapter)) {
                throw new Exception\ErrorException('Adapter class: "' . $adapter . '" does not exist');
            }

            $adapter = new $adapter(
                $adapterUtilities->getDriverParams(),
                $adapterUtilities->getOptionsPlatform(),
                $adapterUtilities->getOptionsQueryResultPrototype(),
                $adapterUtilities->getOptionsProfiler()
            );
        }

        if (!$adapter instanceof AdapterInterface) {
            throw new Exception\ErrorException('Adapter class: "' . $adapter . '" is not of AdapterInterface');
        }

        return $adapter;
    }
}
