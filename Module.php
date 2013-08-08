<?php
namespace LocGatewayManager;

class Module
{
    public function getConfig()
    {
        if (defined('APPLICATION_ENV')) {
            if (APPLICATION_ENV == 'unittest') {
                return include __DIR__ . '/test/config/module.config.php';
            }
        }
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'invokables' => array(
                'LocGatewayManager'  => __NAMESPACE__ . '\Manager',
                'LocGatewayWorker'   => __NAMESPACE__ . '\GatewayWorker',
                'LocGatewayFactory'  => __NAMESPACE__ . '\GatewayFactory',
            ),
            'shared' => array(
                'LocGatewayWorker' => false,
            ),
        );
    }
}
