<?php
namespace FdlGatewayManager;

use FdlGatewayManager\GatewayFactoryUtilities as FactoryUtilities;
use Zend\EventManager;

class GatewayWorkerEventListeners extends AbstractServiceLocatorAware
    implements EventManager\ListenerAggregateInterface
{
    /**
     * @var \Zend\Stdlib\CallbackHandler[]
     */
    protected $listeners = array();

    /**
     * @return \Zend\EventManager\EventManager
     */
    protected $factoryEventManager;

    /**
     * Attach one or more listeners
     *
     * @param EventManagerInterface $events
     * @return void
     */
    public function attach(EventManager\EventManagerInterface $events)
    {
        $this->attachAdapterListeners();
        $this->attachTableNameListeners();
        $this->attachGatewayOptionalParams();
        $this->attachPostInit();
    }

    public function attachAdapterListeners()
    {
        $factoryEventManager = $this->getFactoryEventManager();
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::INIT_ADAPTER, array($this, 'initAdapter'));
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::INIT_ADAPTER, array($this, 'initEntity'));
    }

    public function attachTableNameListeners()
    {
        $factoryEventManager = $this->getFactoryEventManager();
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::RESOLVE_TABLE_NAME, array($this, 'resolveTableName'));
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::RESOLVE_TABLE_GATEWAY, array($this, 'resolveTableGateway'));
    }

    public function attachGatewayOptionalParams()
    {
        $factoryEventManager = $this->getFactoryEventManager();
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::LOAD_FEATURES, array($this, 'loadFeatures'));
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::LOAD_RESULT_SET_PROTOTYPE, array($this, 'loadResultSetPrototype'));
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::LOAD_SQL, array($this, 'loadSql'));
    }

    public function attachPostInit()
    {
        $factoryEventManager = $this->getFactoryEventManager();
        $this->listeners[] = $factoryEventManager->attach(GatewayWorkerEvent::POST_INIT_TABLE_GATEWAY, array($this, 'postInit'), -100);
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

    /**
     * Initialize the TableGateway adapter
     * The adapter is initialize by an abstract factory.
     *
     * If a user want to override the factory, it can be
     * done by overriding 'FdlTableGateway\Adapter'
     *
     * @param GatewayWorkerEvent $e
     */
    public function initAdapter(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $adapterKey     = $e->getAdapterKey() ?: 'default';
        $serviceManager = $gatewayFactory->getServiceLocator();
        $config         = $serviceManager->get('config');

        // pull from the adapter container if adapter exists
        $adapterContainer = $serviceManager->get('FdlGatewayFactoryAdapterKeyContainer');
        if (isset($adapterContainer[$adapterKey])) {
            $adapter = $adapterContainer[$adapterKey];
        } else {
            $adapter = $serviceManager->get($config['fdl_gateway_manager_config']['table_gateway']['adapter']);
            $adapterContainer[$adapterKey] = $adapter;
        }

        $gatewayFactory->setAdapter($adapter);
    }

    /**
     * Initialize the entity if defined
     *
     * @param GatewayWorkerEvent $e
     */
    public function initEntity(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();

        $entity = $serviceManager->get('FdlEntityFactory');
        if (!$entity instanceof \stdClass) {
            $gatewayFactory->setEntity($entity);
        }
    }

    /**
     * Resolve the '$table' argument of TableGateway.
     * Extract the class name string  from the instantiated TableServiceFactory
     * or use the defined entity.
     *
     * @param GatewayWorkerEvent $e
     * @throws Exception\ErrorException
     */
    public function resolveTableName(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();
        $tableClass     = $serviceManager->get('FdlTableServiceFactory');

        // the table object exist
        if (!$tableClass instanceof \stdClass) {
            // table class is an implementation of TableInterface
            if ($tableClass instanceof Gateway\TableInterface && $tableClass->getTableName() !== null) {
                $tableClass =  $tableClass->getTableName();
            } else {
                // try to get the classname from the FQNS of the table class
                $tableClass = FactoryUtilities::extractClassnameFromFQNS($tableClass);
                $tableClass = FactoryUtilities::normalizeTablename($tableClass);
            }
        }

        if (null === $tableClass) {
            throw new Exception\ErrorException('Cannot resolve table name');
        }

        $gatewayFactory->setTable($tableClass);
    }

    /**
     * Resolve the Table Gateway proxy.
     *
     * This can be a TableGateway proxy which is an extended class from
     * FdlGatewayManager\Gateway\AbstractTable
     *
     * If a valid object is returned by the TableServiceFactory then store
     * that object in the Gateway Factory.
     *
     * @param GatewayWorkerEvent $e
     */
    public function resolveTableGateway(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();
        $tableClass     = $serviceManager->get('FdlTableServiceFactory');

        if (!$tableClass instanceof \stdClass) {
            $gatewayFactory->setTableGateway($tableClass);
        }
    }

    /**
     * Load the TableGateway features if set.
     * Pulls from the abstract factory.
     *
     * If a user want to override the factory, it can be
     * done by overriding 'FdlTableGateway\Features'
     *
     * @param GatewayWorkerEvent $e
     */
    public function loadFeatures(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();
        $config         = $serviceManager->get('config');

        // checks if the feature class implements FeatureInterface
        $feature = $serviceManager->get($config['fdl_gateway_manager_config']['table_gateway']['features']);
        if ($feature instanceof \FdlGatewayManager\Feature\AbstractFeature) {
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

    /**
     * Load the TableGateway ResultSetPrototype argument if set.
     * Pulls from the abstract factory.
     *
     * If a user want to override the factory, it can be
     * done by overriding 'FdlTableGateway\ResultSetPrototype'
     *
     * @param GatewayWorkerEvent $e
     */
    public function loadResultSetPrototype(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();
        $config         = $serviceManager->get('config');

        // checks if the result set prototype class implements FeatureInterface
        $resultSetPrototype = $serviceManager->get($config['fdl_gateway_manager_config']['table_gateway']['result_set_prototype']);
        if ($resultSetPrototype instanceof \FdlGatewayManager\ResultSet\AbstractResultSet) {
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
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();
        $config         = $serviceManager->get('config');

        // checks if the result set prototype class implements FeatureInterface
        $sql = $serviceManager->get($config['fdl_gateway_manager_config']['table_gateway']['sql']);
        if ($sql instanceof \FdlGatewayManager\Sql\AbstractSql) {
            $sql->setGatewayFactory($gatewayFactory)
                ->create();
            $gatewayFactory->setSql($sql->getSql());
        } else {
            // we cannot return a null on an abstract factory so check for stdClass
            if (!$sql instanceof \stdClass) {
                $gatewayFactory->setSql($sql);
            }
        }
    }

    public function postInit(GatewayWorkerEvent $e)
    {
        $gatewayFactory = $e->getTarget();
        $serviceManager = $gatewayFactory->getServiceLocator();
        $tableGateway   = $serviceManager->get('FdlTableGatewayServiceFactory');

        // If table gateway proxy exists then insert the TableGateway instance in it.
        // Note that TableGateway gets initialize by the Table proxy instance
        // in resolveTableGateway event
        $tableGatewayProxy = $gatewayFactory->getTableGateway();
        if (isset($tableGatewayProxy) && $tableGatewayProxy instanceof Gateway\AbstractTable) {
            $tableGateway = $tableGatewayProxy->setTableGateway($tableGateway);
        }

        return $tableGateway;
    }

    /**
     * Return the specific Gateway Factory Event Manager
     * @return \Zend\EventManager\EventManager
     */
    public function getFactoryEventManager()
    {
        $factory = $this->getServiceLocator()->get('FdlGatewayFactory');
        return $factory->getEventManager();
    }
}
