<?php
namespace FdlGatewayManager\Factory;

use FdlGatewayManager\GatewayFactoryUtilities as FactoryUtilities;
use Zend\ServiceManager;

class TableServiceFactory implements ServiceManager\FactoryInterface
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
        $event          = $serviceLocator->get('FdlGatewayFactory')->getWorkerEvent();
        $adapterKeyName = $event->getAdapterKey();
        $tableName      = $event->getTableName();
        $assetLocation  = $config['fdl_gateway_manager_config']['asset_location'];

        if (null !== $adapterKeyName) {
            if (isset($assetLocation[$adapterKeyName]['tables'])) {
                $tableNamespace = $assetLocation[$adapterKeyName]['tables'];
            }
        }

        // no adapter key config located use 'default'
        // or if there is no default, just use the tables array
        if (!isset($tableNamespace)) {
            if (isset($assetLocation['default']['tables'])) {
                $tableNamespace = $assetLocation['default']['tables'];
            } elseif (isset($assetLocation['tables'])) {
                $tableNamespace = $assetLocation['tables'];
            }
        }

        // if tableName does not exist, try the entity name
        if (null === $tableName) {
            $entity = $serviceLocator->get('FdlGatewayFactory')->getEntity();
            if (isset($entity)) {
                $entity = FactoryUtilities::extractClassnameFromFQNS($entity);
                $entity = FactoryUtilities::normalizeTablename($entity);
                $tableName = $entity;
            }
        }

        $tableClass = $tableNamespace . '\\' . $tableName;
        if (class_exists($tableClass)) {
            return new $tableClass();
        } else {
            // maybe a class with an appended 'Table' exists then use it
            $tableClass = $tableClass . 'Table';
            if (class_exists($tableClass)) {
                return new $tableClass();
            }

            return new \stdClass();
        }
    }
}
