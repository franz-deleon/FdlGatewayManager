<?php
namespace FdlGatewayManager\Factory;

use FdlGatewayManager\Exception;
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
        $config  = $serviceLocator->get('config');
        $event = $serviceLocator->get('FdlGatewayFactoryEvent');
        $adapterKeyName = $event->getAdapterKey();
        $tableName      = $event->getTableName();
        $assetLocation  = $config['fdl_gateway_manager_config']['asset_location'];

        if (null !== $adapterKeyName) {
            if (isset($assetLocation[$adapterKeyName]['tables'])) {
                $tableNamespace = $assetLocation[$adapterKeyName]['tables'];
            }
        }

        if (!isset($tableNamespace)) {
            if (isset($assetLocation['default']['tables'])) {
                $tableNamespace = $assetLocation['default']['tables'];
            } elseif (isset($assetLocation['tables'])) {
                $tableNamespace = $assetLocation['tables'];
            }
        }

        $tableClass = $tableNamespace . '\\' . $tableName;
        if (class_exists($tableClass)) {
            return new $tableClass();
        } else {
            $tableClass = $tableClass . 'Table';
            if (class_exists($tableClass)) {
                return $tableClass();
            }

            throw new Exception\ClassNotExistException('Table class: ' . $tableClass . ' does not exist.');
        }
    }
}
