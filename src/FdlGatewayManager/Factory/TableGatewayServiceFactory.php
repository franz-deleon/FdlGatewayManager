<?php
namespace FdlGatewayManager\Factory;

use Zend\ServiceManager;

class TableGatewayServiceFactory implements ServiceManager\FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $gatewayFactory = $serviceLocator->get('FdlGatewayFactory');
        $config         = $serviceLocator->get('config');
        $event          = $serviceLocator->get('FdlGatewayWorkerEvent');
        $tableGateway   = $config['fdl_gateway_manager_config']['gateway'];

        $tableGateway = new $tableGateway(
            $gatewayFactory->getTable(),
            $gatewayFactory->getAdapter(),
            $gatewayFactory->getFeature(),
            $gatewayFactory->getResultSetPrototype(),
            $gatewayFactory->getSql()
        );

        return $tableGateway;
    }
}
