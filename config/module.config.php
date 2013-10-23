<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'LocGatewayPlugin' => 'LocGatewayManager\LocGatewayPluginFactory',
        ),
    ),
    'loc_gateway_manager_assets' => array(
        'gateway'  => 'default',
    ),
    'loc_gateway_manager_gateways' => array(
        'default' => 'Zend\Db\TableGateway\TableGateway',
    ),
    'loc_service_listener_options' => array(
        'service_manager' => 'LocGatewayPlugin',
        'config_key'      => 'loc_gateway_plugin_config',
        'interface'       => 'LocGatewayManager\LocGatewayPluginProviderInterface',
        'method'          => 'getLocGatewayPluginConfig',
    ),
);
