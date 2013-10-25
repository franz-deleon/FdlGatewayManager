<?php
namespace FdlGatewayManager;

use Zend\Db\TableGateway;

class GatewayManager extends AbstractServiceLocatorAware
{
    /**
     * Usage:
     * Manager::factory(array(
     *     'adapter_key_name' => 'oracle',
     *     'table_name'       => 'BOOKS',
     *     'entity_name'      => 'Books',
     *     'feature_name'     => 'RowGatewayFeature'
     *     'result_set_name'  => 'HydratingResultSet'
     * ), 'books-hydrating');
     *
     * @param array $params
     * @param string $index
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function factory(array $params)
    {
        $event   = $this->getServiceLocator()->get('FdlGatewayFactoryEvent');
        $worker  = $this->getServiceLocator()->get('FdlGatewayWorker');

        if (isset($params['adapter_key_name'])) {
            $worker->setAdapterKeyName($params['adapter_key_name']);
            $event->setAdapterKey($params['adapter_key_name']);
        }
        if (isset($params['entity_name'])) {
            $worker->setEntityName($params['entity_name']);
            $event->setEntityName($params['entity_name']);
        }
        if (isset($params['feature_name'])) {
            $worker->setFeatureName($params['feature_name']);
            $event->setFeatureName($params['feature_name']);
        }
        if (isset($params['result_set_name'])) {
            $worker->setResultSetName($params['result_set_name']);
            $event->setResultSetPrototypeName($params['result_set_name']);
        }
        if (isset($params['table_name'])) {
            $worker->setTableName($params['table_name']);
        }

        // set the worker
        $factory = $this->getFactory()->setWorker($worker);

        // load the adapter
        $this->load();

        $factory->run();
        $tableGateway = $factory->getTableGateway();

        //reset the factory
        $factory->reset();

        return $tableGateway;
    }

    public function load()
    {
        $this->getFactory()->getEventManager()->attach(GatewayFactoryEvent::LOAD_ADAPTER, function ($e) {
            $gatewayFactory = $e->getTarget();
            $adapterKey     = $e->getAdapterKey() ?: 'default';
            $serviceManager = $gatewayFactory->getServiceLocator();
            $config         = $serviceManager->get('config');
            $adapterContainer = $serviceManager->get('FdlGatewayFactoryAdapterKeyContainer');

            // pull from the adapter container if adapter exists
            if (isset($adapterContainer[$adapterKey])) {
                $adapter = $adapterContainer[$adapterKey];
            } else {
                $adapter = $serviceManager->get($config['fdl_gateway_manager_config']['table_gateway']['adapter']);
                $adapterContainer[$adapterKey] = $adapter;
            }

            $gatewayFactory->setAdapter($adapter);
        });

        $this->getFactory()->getEventManager()->attach(GatewayFactoryEvent::LOAD_ADAPTER, function ($e) {
            $gatewayFactory = $e->getTarget();
            $serviceManager = $gatewayFactory->getServiceLocator();

            $entity = $serviceManager->get('FdlEntityFactory');
            $gatewayFactory->setEntity($entity);
        });

        $this->getFactory()->getEventManager()->attach(GatewayFactoryEvent::LOAD_FEATURES, function ($e) {
            $gatewayFactory = $e->getTarget();
            $serviceManager = $gatewayFactory->getServiceLocator();
            $config         = $serviceManager->get('config');

            // checks if the feature class implements FeatureInterface
            $feature = $serviceManager->get($config['fdl_gateway_manager_config']['table_gateway']['features']);
            if ($feature instanceof \FdlGatewayManager\Feature\FeatureInterface) {
                $feature->setGatewayFactory($gatewayFactory)
                        ->create();
                $gatewayFactory->setFeature($feature->getFeature());
            } else {
                // we cannot return a null on an abstract factory so check for stdClass
                if (!$feature instanceof \stdClass) {
                    $gatewayFactory->setFeature($feature);
                }
            }
        });

        $this->getFactory()->getEventManager()->attach(GatewayFactoryEvent::LOAD_RESULT_SET_PROTOTYPE, function ($e) {
            $gatewayFactory = $e->getTarget();
            $serviceManager = $gatewayFactory->getServiceLocator();
            $config         = $serviceManager->get('config');

            // checks if the feature class implements FeatureInterface
            $resultSetPrototype = $serviceManager->get($config['fdl_gateway_manager_config']['table_gateway']['result_set_prototype']);
            if ($resultSetPrototype instanceof \FdlGatewayManager\ResultSet\ResultSetInterface) {
                $resultSetPrototype->setGatewayFactory($gatewayFactory)
                                   ->create();
                $gatewayFactory->setResultSetPrototype($resultSetPrototype->getResultSetPrototype());
            } else {
                // we cannot return a null on an abstract factory so check for stdClass
                if (!$resultSetPrototype instanceof \stdClass) {
                    $gatewayFactory->setResultSetPrototype($resultSetPrototype);
                }
            }
        });
    }

    public function getFactory()
    {
        return $this->getServiceLocator()->get('FdlGatewayFactory');
    }
}
