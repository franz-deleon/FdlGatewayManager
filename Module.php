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
            'invokables' => array(
                'FdlAdapterPreparerUtility'            => 'FdlGatewayManager\Utils\AdapterPreparerUtility',
                'FdlGatewayFactoryUtilities'           => 'FdlGatewayManager\Utils\GatewayFactoryUtilities',
                'FdlAdapterManager'                    => 'FdlGatewayManager\AdapterManager',
                'FdlGatewayFactory'                    => 'FdlGatewayManager\GatewayFactory',
                'FdlGatewayWorkerEvent'                => 'FdlGatewayManager\GatewayWorkerEvent',
                'FdlGatewayFactoryEvent'               => 'FdlGatewayManager\GatewayFactoryEvent',
                'FdlGatewayWorkerEventListeners'       => 'FdlGatewayManager\GatewayWorkerEventListeners',
            ),
            'factories' => array(
                'FdlGatewayPlugin'               => 'FdlGatewayManager\Service\FdlGatewayPluginFactory',
                'FdlEntityFactory'               => 'FdlGatewayManager\Factory\EntityServiceFactory',
                'FdlTableServiceFactory'         => 'FdlGatewayManager\Factory\TableServiceFactory',
                'FdlTableGatewayServiceFactory'  => 'FdlGatewayManager\Factory\TableGatewayServiceFactory',
                'FdlGatewayManager'              => function ($sm) {
                    return new GatewayManager($sm);
                },
            ),
            'abstract_factories' => array(
                'FdlGatewayManager\AbstractFactory\AdapterServiceAbstractFactory',
                'FdlGatewayManager\AbstractFactory\FeaturesServiceAbstractFactory',
                'FdlGatewayManager\AbstractFactory\ResultSetPrototypeServiceAbstractFactory',
            ),
            'shared' => array(
                // worker factories
                'FdlGatewayWorkerEvent' => false,
                'FdlGatewayWorkerEventListeners' => false,

                // event factories
                'FdlTableServiceFactory' => false,
                'FdlEntityFactory' => false,
                'FdlTableGatewayServiceFactory' => false,

                // abstract factories
                'FdlTableGateway\Adapter' => false,
                'FdlTableGateway\Features' => false,
                'FdlTableGateway\ResultSetPrototype' => false,
            ),
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
