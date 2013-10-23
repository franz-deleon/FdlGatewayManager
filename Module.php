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
        if (defined('APPLICATION_ENV')) {
            if (APPLICATION_ENV == 'unittest') {
                return include __DIR__ . '/test/FdlGatewayManagerTest/config/module.config.php';
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
                'FdlGatewayManager'  => __NAMESPACE__ . '\Manager',
                'FdlGatewayWorker'   => __NAMESPACE__ . '\GatewayWorker',
                'FdlGatewayFactoryUtilities' => __NAMESPACE__ . '\GatewayFactoryUtilities',
            ),
            'factories' => array(
                'FdlGatewayFactory' => function ($sm) {
                    return new GatewayFactory($sm->get('FdlGatewayFactoryUtilities'));
                },
                'FdlGatewayTableGateway' => function ($sm) {
                    $gwfactory    = $sm->get('FdlGatewayFactory');
                    $factoryUtils = $sm->get('FdlGatewayFactoryUtilities');

                    // initialize a gateway
                    $gateway = $factoryUtils->getConfigGatewayName();
                    $gateway = new $gateway(
                        $gwfactory->getTable(),
                        $gwfactory->getAdapter(),
                        $gwfactory->getFeature(),
                        $gwfactory->getResultSet()
                    );

                    // inject to the abstract table if any
                    $tableTarget = $gwfactory->getTableGatewayProxy();
                    if (isset($tableTarget)) {
                        $tableTarget = new $tableTarget();
                        if ($tableTarget instanceof Gateway\AbstractTable) {
                            $gateway = $tableTarget->setTableGateway($gateway);
                        }
                    }

                    return $gateway;
                },
            ),
            'shared' => array(
                'FdlGatewayWorker' => false,
                'FdlGatewayTableGateway' => false,
            ),
            'initializers' => array(
                function ($instance, $sm) {
                    if ($instance instanceof FdlGatewayPluginAwareInterface) {
                        $instance->setGatewayPlugin($sm->get('FdlGatewayPlugin'));
                    }
                },
            ),
        );
    }
}
