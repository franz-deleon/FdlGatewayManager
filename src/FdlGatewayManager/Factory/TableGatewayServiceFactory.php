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
        $config         = $serviceLocator->get('config');
        $tableGateway   = $config['fdl_gateway_manager_config']['gateway'];

        if ((is_array($tableGateway) || is_string($tableGateway))
            && $serviceLocator->has($tableGateway)
        ) {
            $tableGateway = $serviceLocator->get($tableGateway);
        } else {
            // no direct implementation of TableGateway
            $gatewayFactory = $serviceLocator->get('FdlGatewayFactory');
            $tableGateway = new $tableGateway(
                $gatewayFactory->getTable(),
                $gatewayFactory->getAdapter(),
                $gatewayFactory->getFeature(),
                $gatewayFactory->getResultSetPrototype(),
                $gatewayFactory->getSql()
            );
        }
        return $tableGateway;
    }
}
