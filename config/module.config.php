<?php
return array(
    'service_manager' => array(
        'invokables' => array(
            'FdlGatewayFactoryAdapterKeyContainer' => 'FdlGatewayManager\GatewayFactoryAdapterKeyContainer',
        ),
        'factories' => array(
            'FdlGatewayPlugin' => 'FdlGatewayManager\Service\FdlGatewayPluginFactory',
            'FdlEntityFactory' => 'FdlGatewayManager\Factory\EntityServiceFactory',
            'FdlTableFactory'  => 'FdlGatewayManager\Factory\TableServiceFactory',
        ),
        'abstract_factories' => array(
            //'FdlGatewayManager\Factory\TableAbstractFactory',
            'FdlGatewayManager\Factory\AdapterServiceAbstractFactory',
            'FdlGatewayManager\Factory\FeaturesServiceAbstractFactory',
            'FdlGatewayManager\Factory\ResultSetPrototypeServiceAbstractFactory',
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
         *     factories => array(
         *         'FdlTableGateway\Table' => function ($sm) {},
         *     )
         * </code>
         */
        'table_gateway' => array(
            'table'    => 'FdlTableGateway\Table',
            'adapter'  => 'FdlTableGateway\Adapter',
            'features' => 'FdlTableGateway\Features',
            'sql'      => 'FdlTableGateway\Sql',
            'result_set_prototype' => 'FdlTableGateway\ResultSetPrototype',
        ),
        /**
         * These is where to store the table and entity mapping classes.
         * Values are namespaces.
         *
         * IMPORTANT: these are to be filled in the implementing module
         */
        'asset_location' => array(
            'tables'   => '',
            'entities' => '',
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
