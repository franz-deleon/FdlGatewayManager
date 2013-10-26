<?php
namespace FdlGatewayManager;

use Zend\EventManager;

class GatewayWorkerEventListeners extends AbstractServiceLocatorAware
    implements EventManager\ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * Attach one or more listeners
     *
     * @param EventManagerInterface $events
     * @return void
     */
    public function attach(EventManager\EventManagerInterface $events)
    {
        $factory = $this->getServiceLocator()->get('FdlGatewayFactory');
        $factoryEventManager = $factory->getEventManager();

        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::INIT_ADAPTER, array($this, 'initAdapter'));
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::INIT_ADAPTER, array($this, 'initEntity'));
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::RESOLVE_TABLE, array($this, 'resolveTable'));
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::LOAD_FEATURES, array($this, 'loadFeatures'));
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::LOAD_RESULT_SET_PROTOTYPE, array($this, 'loadResultSetPrototype'));
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::LOAD_SQL, array($this, 'loadSql'));
    }

    /**
     * Detach all previously attached listeners
     *
     * @param EventManagerInterface $events
     * @return void
    */
    public function detach(EventManager\EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {
            if ($events->detach($listener)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function initAdapter(GatewayWorkerEvent $e)
    {
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
    }

    public function initEntity(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();

        $entity = $serviceManager->get('FdlEntityFactory');
        $gatewayFactory->setEntity($entity);
    }

    public function resolveTable(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();
        $config         = $serviceManager->get('config');
        $tableClass     = $serviceManager->get('FdlTableServiceFactory');

        // the table object exist so check if the actual table name exist
        if (!$tableClass instanceof \stdClass) {
            $tableClass = new $tableClass();
            if ($tableClass instanceof Gateway\TableInterface && $tableClass->getTableName() !== null) {
                return $tableClass->getTableName();
            } elseif (!empty($tableClass->tableName)) {
                return $this->tableName;
            } else {
                $tableClass = $this->extractClassnameFromNamespace($tableClass);
                return $this->normalizeTablename($tableClass, $adapter);
            }
        }
    }

    public function loadFeatures(GatewayWorkerEvent $e)
    {
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
    }

    public function loadResultSetPrototype(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();
        $config         = $serviceManager->get('config');

        // checks if the result set prototype class implements FeatureInterface
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
    }

    public function loadSql(GatewayWorkerEvent $e)
    {

    }
}
