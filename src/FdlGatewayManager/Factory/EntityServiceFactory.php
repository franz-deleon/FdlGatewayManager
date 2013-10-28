<?php
namespace FdlGatewayManager\Factory;

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
        $event = $serviceLocator->get('FdlGatewayWorkerEvent');
        $adapterKeyName = $event->getAdapterKey();
        $entityName     = $event->getEntityName();
        $assetLocation  = $config['fdl_gateway_manager_config']['asset_location'];

        if (null !== $adapterKeyName) {
            if (isset($assetLocation[$adapterKeyName]['entities'])) {
                $entityNamespace = $assetLocation[$adapterKeyName]['entities'];
            }
        }

        if (!isset($entityNamespace)) {
            if (isset($assetLocation['default']['entities'])) {
                $entityNamespace = $assetLocation['default']['entities'];
            } elseif (isset($assetLocation['entities'])) {
                $entityNamespace = $assetLocation['entities'];
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
        }

        return new \stdClass();
    }
}
