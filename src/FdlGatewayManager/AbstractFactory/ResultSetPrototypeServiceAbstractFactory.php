<?php
namespace FdlGatewayManager\AbstractFactory;

use FdlGatewayManager\Utils\GatewayFactoryUtilities as FactoryUtilities;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\ServiceManager;

class ResultSetPrototypeServiceAbstractFactory implements ServiceManager\AbstractFactoryInterface
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
        $serviceName = $config['fdl_gateway_manager_config']['table_gateway']['result_set_prototype'];

        return ($requestedName === $serviceName);
    }

    /**
     * Create service with name
     *
     * If set to "Default" or "HydratingResultSet",
     * Will default by creating a HydratingResult object and injects
     * The current entity object if it exists
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $gatewayFactory = $serviceLocator->get('FdlGatewayFactory');
        $resultSetPrototypeName = FactoryUtilities::normalizeClassname($gatewayFactory->getWorkerEvent()->getResultSetPrototypeName());

        if (isset($resultSetPrototypeName)
            && ($resultSetPrototypeName === 'Default' || $resultSetPrototypeName === 'HydratingResultSet')
        ) {
            return new HydratingResultSet(
                new \Zend\Stdlib\Hydrator\ClassMethods(true),
                $gatewayFactory->getEntity()
            );
        }

        return new \stdClass();
    }
}
