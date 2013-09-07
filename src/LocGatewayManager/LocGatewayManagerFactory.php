<?php
namespace LocGatewayManager;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocGatewayManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'LocGatewayManagerPluginManager';

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
