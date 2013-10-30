<?php
namespace FdlGatewayManager\AbstractFactory;

use FdlGatewayManager\Utils\GatewayFactoryUtilities as FactoryUtilities;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\ServiceManager;

class FeaturesServiceAbstractFactory implements ServiceManager\AbstractFactoryInterface
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
        $serviceName = $config['fdl_gateway_manager_config']['table_gateway']['features'];

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
        $gatewayFactory = $serviceLocator->get('FdlGatewayFactory');
        $featureName    = FactoryUtilities::normalizeClassname($gatewayFactory->getWorkerEvent()->getFeatureName());

        if (isset($featureName)
            && ($featureName === 'Default' || $featureName == 'RowGatewayFeature')
        ) {
            $tableProxy = $gatewayFactory->getTableGateway();
            if (isset($tableProxy) && is_object($tableProxy)) {
                if (property_exists($tableProxy, 'primaryKey')) {
                    $primaryKey = $tableProxy->primaryKey;
                } elseif (is_callable(array($tableProxy, 'getPrimaryKey')) && $tableProxy->getPrimaryKey() !== null) {
                    $primaryKey = $tableProxy->getPrimaryKey();
                }
            }

            if (isset($primaryKey)) {
                return new RowGatewayFeature($primaryKey);
            }

            return new RowGatewayFeature();
        }

        return new \stdClass();
    }
}
