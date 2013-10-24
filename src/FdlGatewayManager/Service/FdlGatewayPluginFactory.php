<?php
namespace FdlGatewayManager\Service;

use Zend\Mvc\Service\AbstractPluginManagerFactory;
use Zend\ServiceManager\ConfigInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FdlGatewayPluginFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'FdlGatewayManager\Service\FdlGatewayPluginManager';

    /**
     * Create and return the FdlGateway plugin manager
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
