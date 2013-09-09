<?php
namespace LocGatewayManager;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocGatewayPluginFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'LocGatewayManager\LocGatewayPluginManager';

    /**
     * Create and return the LocGateway plugin manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return \Zend\Filter\FilterPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);
        return $plugins;
    }
}
