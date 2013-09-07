<?php
namespace LocGatewayManager;

class Module
{
    /*
    public function init($moduleManager)
    {
        $sm = $moduleManager->getEvent()->getParam('ServiceManager');
        $serviceListener = $sm->get('ServiceListener');
        $serviceListener->addServiceManager(
            __NAMESPACE__ . '\LocGatewayManagerPluginManager',
            'loc_gateway_plugin_config',
            __NAMESPACE__ . '\LocGatewayManagerPluginProviderInterface',
            'getLocGatewayPluginConfig'
        );
    }
*/
    public function getConfig()
    {
        if (defined('APPLICATION_ENV')) {
            if (APPLICATION_ENV == 'unittest') {
                return include __DIR__ . '/test/LocGatewayManagerTest/config/module.config.php';
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
                'LocGatewayFactoryUtilities' => __NAMESPACE__ . '\GatewayFactoryUtilities',
            ),
            'factories' => array(
                'LocGatewayFactory' => function ($sm) {
                    return new GatewayFactory($sm->get('LocGatewayFactoryUtilities'));
                },
                'LocGatewayTableGateway' => function ($sm) {
                    $gwfactory    = $sm->get('LocGatewayFactory');
                    $factoryUtils = $sm->get('LocGatewayFactoryUtilities');

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
                'LocGatewayWorker' => false,
                'LocGatewayTableGateway' => false,
            ),
            'initializers' => array(
                function ($instance, $sm) {
                    if ($instance instanceof LocGatewayManagerAwareInterface) {
                        $instance->setLocGatewayManager($sm->get('LocGatewayManager'));
                    }
                },
            ),
        );
    }
}
