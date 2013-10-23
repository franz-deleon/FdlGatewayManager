<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'FdlGatewayPlugin' => 'FdlGatewayManager\FdlGatewayPluginFactory',
        ),
    ),
    'fdl_gateway_manager_assets' => array(
        'gateway'  => 'default',
    ),
    'fdl_gateway_manager_gateways' => array(
        'default' => 'Zend\Db\TableGateway\TableGateway',
    ),
    'fdl_service_listener_options' => array(
        'service_manager' => 'FdlGatewayPlugin',
        'config_key'      => 'fdl_gateway_plugin_config',
        'interface'       => 'FdlGatewayManager\FdlGatewayPluginProviderInterface',
        'method'          => 'getFdlGatewayPluginConfig',
    ),
);
