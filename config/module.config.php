<?php
return array(
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
         *         'FdlTableGateway\Adapter' => function ($sm) {},
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
        /**
         * Factory helper to hook to the GatewayFactory
         */
        'factory_event_hook' => 'FdlFactoryEventHook',
     ),
    'fdl_service_listener_options' => array(
        'service_manager' => 'FdlGatewayPlugin',
        'config_key'      => 'fdl_gateway_plugin_config',
        'interface'       => 'FdlGatewayManager\Service\FdlGatewayPluginProviderInterface',
        'method'          => 'getFdlGatewayPluginConfig',
    ),
);
