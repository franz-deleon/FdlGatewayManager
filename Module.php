<?php
namespace FdlGatewayManager;

class Module
{

    public function init($moduleManager)
    {
        $config = $this->getConfig();
        $listener = $moduleManager->getEvent()->getParam('ServiceManager')->get('ServiceListener');

        $listener->addServiceManager(
            $config['fdl_service_listener_options']['service_manager'],
            $config['fdl_service_listener_options']['config_key'],
            $config['fdl_service_listener_options']['interface'],
            $config['fdl_service_listener_options']['method']
        );
    }

    public function getConfig()
    {
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
            'initializers' => array(
                function ($instance, $sm) {
                    if ($instance instanceof GatewayPluginAwareInterface) {
                        $instance->setGatewayPlugin($sm->get('FdlGatewayPlugin'));
                    }
                },
            ),
        );
    }
}
