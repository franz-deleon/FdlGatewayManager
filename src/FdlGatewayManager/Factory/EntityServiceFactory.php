<?php
namespace FdlGatewayManager\Factory;

use Zend\Db;
use Zend\ServiceManager;

class EntityServiceFactory implements ServiceManager\FactoryInterface
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
        $entityName     = $event->getEntityName();

        if (null !== $adapterKeyName) {
            if (isset($config['fdl_gateway_manager_config']['asset_location'][$adapterKeyName]['entities'])) {
                $entityNamespace = $config['fdl_gateway_manager_config']['asset_location'][$adapterKeyName]['entities'];
            }
        }

        if (!isset($entityNamespace)) {
            if (isset($config['fdl_gateway_manager_config']['asset_location']['default']['entities'])) {
                $entityNamespace = $config['fdl_gateway_manager_config']['asset_location']['default']['entities'];
            } elseif (isset($config['fdl_gateway_manager_config']['asset_location']['entities'])) {
                $entityNamespace = $config['fdl_gateway_manager_config']['asset_location']['entities'];
            }
        }

        $entity = $entityNamespace . '\\' . $entityName;
        if (class_exists($entity)) {
            return new $entity();
        } else {
            $entity = $entity . 'Entity';
            if (class_exists($entity)) {
                return $entity();
            }

            throw new Exception\ClassNotExistException('Entity ' . $entity . ' does not exist.');
        }
    }
}
