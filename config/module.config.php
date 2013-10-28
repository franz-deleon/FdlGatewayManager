<?php
return array(
    'service_manager' => array(
        'invokables' => array(
            'FdlGatewayManager'                    => 'FdlGatewayManager\GatewayManager',
            'FdlGatewayFactory'                    => 'FdlGatewayManager\GatewayFactory',
            'FdlGatewayWorkerEvent'                => 'FdlGatewayManager\GatewayWorkerEvent',
            'FdlGatewayWorkerEventListeners'       => 'FdlGatewayManager\GatewayWorkerEventListeners',
            'FdlGatewayFactoryAdapterKeyContainer' => 'FdlGatewayManager\GatewayFactoryAdapterKeyContainer',
        ),
        'factories' => array(
            'FdlGatewayPlugin'               => 'FdlGatewayManager\Service\FdlGatewayPluginFactory',
            'FdlEntityFactory'               => 'FdlGatewayManager\Factory\EntityServiceFactory',
            'FdlTableServiceFactory'         => 'FdlGatewayManager\Factory\TableServiceFactory',
            'FdlTableGatewayServiceFactory'  => 'FdlGatewayManager\Factory\TableGatewayServiceFactory',
        ),
        'abstract_factories' => array(
            'FdlGatewayManager\Factory\AdapterServiceAbstractFactory',
            'FdlGatewayManager\Factory\FeaturesServiceAbstractFactory',
            'FdlGatewayManager\Factory\ResultSetPrototypeServiceAbstractFactory',
            'FdlGatewayManager\Factory\SqlServiceAbstractFactory',
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
            'FdlTableGateway\Sql' => false,
            'FdlTableGateway\ResultSetPrototype' => false,
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
         *
         * IMPORTANT: needs to be a Fully Qualified Namespace
         */
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
