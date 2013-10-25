<?php
return array(
    'service_manager' => array(
        'factories' => array(
            'FdlGatewayPlugin' => 'FdlGatewayManager\Service\FdlGatewayPluginFactory',
        ),
        'abstract_factories' => array(
            'FdlGatewayManager\Factory\TableAbstractFactory',
        ),
    ),
    'fdl_gateway_manager_config' => array(
        /**
         * These are the parameters of Zend\Db\TableGateway\TableGateway.
         * Each parameters are invoked in default by their corresponding
         * Abstract Factory class for their default values.
         *
         * To implement your own class, simply override the values and
         * use it as your service manager key name
         *
         * Example:
         * <code>
         *     factories => array('FdlTableGateway\Table' => function ($sm) {})
         * </code>
         */
        'table_gateway' => array(
            'table'    => 'FdlTableGateway\Table',
            'adapter'  => 'FdlTableGateway\Adapter',
            'features' => 'FdlTableGateway\Features',
            'result_set_prototype' => 'FdlTableGateway\ResultSetPrototype',
            'sql'      => 'FdlTableGateway\Sql',
        ),
        /**
         * These is where to store the table and entity mapping classes.
         * Values are namespaces
         */
        'asset_location' => array(
            'tables'   => '', // to be filled in implementing module
            'entities' => '', // to be filled in implementing module
        ),
        /**
         * Default classes to use for TableGateways and Adapter
         */
        'gateway' => 'Zend\Db\TableGateway\TableGateway',
        'adapter' => 'Zend\Db\Adapter\Adapter',
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
