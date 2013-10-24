<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'FdlGatewayPlugin' => 'FdlGatewayManager\Service\FdlGatewayPluginFactory',
        ),
    ),
    'fdl_gateway_manager' => array(
        'gateway' => 'Zend\Db\TableGateway\TableGateway',
        'adapter' => 'Zend\Db\Adapter\Adapter',
    ),
    'fdl_service_listener_options' => array(
        'service_manager' => 'FdlGatewayPlugin',
        'config_key'      => 'fdl_gateway_plugin_config',
        'interface'       => 'FdlGatewayManager\Service\FdlGatewayPluginProviderInterface',
        'method'          => 'getFdlGatewayPluginConfig',
    ),
);
