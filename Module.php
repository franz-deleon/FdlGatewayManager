<?php
namespace LocGatewayManager;

class Module
{
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
            ),
            'factories' => array(
                'LocGatewayFactory' =>  function ($sm) {
                    $worker = $sm->get('LocGatewayWorker');
                    return new GatewayFactory($worker);
                },
                'LocGatewayTableGateway' => function ($sm) {
                    $config = $sm->get('config');
                    if (!empty($config['loc_gateway_manager_assets']['gateway'])) {
                        $gatewayName = $config['loc_gateway_manager_assets']['gateway'];
                        if (!empty($config['loc_gateway_manager_gateways'][$gatewayName])) {
                            $gateway = $config['loc_gateway_manager_gateways'][$gatewayName];
                        }
                    } else {
                        $gateway = $config['loc_gateway_manager_gateways']['default'];
                    }

                    $gwfactory = $sm->get('LocGatewayFactory');
                    return new $gateway(
                        $gwfactory->getTable(),
                        $gwfactory->getAdapter(),
                        $gwfactory->getFeature(),
                        $gwfactory->getResultSet()
                    );
                }
            ),
        );
    }
}
