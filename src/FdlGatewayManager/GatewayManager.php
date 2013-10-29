<?php
namespace FdlGatewayManager;

use Zend\ServiceManager;

class GatewayManager extends AbstractServiceLocatorAware
{
    /**
     * @var \Zend\EventManager\EventManager
     */
    protected $factoryEventManager;

    /**
     * Constructor
     * Allow to hook into the events
     */
    public function __construct(ServiceManager\ServiceLocatorInterface $serviceManager)
    {
        $gatewayFactory = $serviceManager->get('FdlGatewayFactory');
        $this->factoryEventManager = $gatewayFactory->getEventManager();

        // trigger the pre run hook, the pre run hook will only run once in the life of the factory
        $this->factoryEventManager->trigger(
            GatewayFactoryEvent::ON_MANAGER_STARTUP,
            $gatewayFactory,
            $serviceManager->get('FdlGatewayFactoryEvent')
        );
    }

    /**
     * Usage:
     * <code>
     *     Manager::factory(array(
     *         'adapter_key_name' => 'oracle',
     *         'table_name'       => 'BOOKS',
     *         'entity_name'      => 'Books',
     *         'feature_name'     => 'RowGatewayFeature',
     *         'result_set_name'  => 'HydratingResultSet',
     *         'sql'              => 'Sql'
     *     ), 'books-hydrating');
     * </code>
     * @param array $params
     * @param string $index
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function factory(array $params)
    {
        $workerEvent = $this->getServiceLocator()->get('FdlGatewayWorkerEvent');

        if (isset($params['adapter_key'])) {
            $workerEvent->setAdapterKey($params['adapter_key']);
        } elseif (isset($params['adapter_key_name'])) {
            $workerEvent->setAdapterKey($params['adapter_key_name']);
        }

        if (isset($params['entity'])) {
            $workerEvent->setEntityName($params['entity']);
        } elseif (isset($params['entity_name'])) {
            $workerEvent->setEntityName($params['entity_name']);
        }

        if (isset($params['table'])) {
            $workerEvent->setTableName($params['table']);
        } elseif (isset($params['table_name'])) {
            $workerEvent->setTableName($params['table_name']);
        }

        if (isset($params['feature'])) {
            $workerEvent->setFeatureName($params['feature']);
        } elseif (isset($params['feature_name'])) {
            $workerEvent->setFeatureName($params['feature_name']);
        }

        if (isset($params['result_set'])) {
            $workerEvent->setResultSetPrototypeName($params['result_set']);
        } elseif (isset($params['result_set_name'])) {
            $workerEvent->setResultSetPrototypeName($params['result_set_name']);
        } elseif (isset($params['result_set_prototype'])) {
            $workerEvent->setResultSetPrototypeName($params['result_set_prototype']);
        } elseif (isset($params['result_set_prototype_name'])) {
            $workerEvent->setResultSetPrototypeName($params['result_set_prototype_name']);
        }

        if (isset($params['sql'])) {
            $workerEvent->setSql($params['sql']);
        } elseif (isset($params['sql_name'])) {
            $workerEvent->setSql($params['sql_name']);
        }

        return $this->createTableGateway($workerEvent);
    }

    /**
     * Create the TableGateway
     *
     * @param \FdlGatewayManager\GatewayWorkerEVent $workerEvent
     * @return \Zend\Db\TableGateway\TableGatewayInterface
     */
    public function createTableGateway($workerEvent)
    {
        // attach the listeners
        $serviceManager       = $this->getServiceLocator();
        $factoryEventManager  = $this->factoryEventManager;
        $workerEventListeners = $serviceManager->get('FdlGatewayWorkerEventListeners');
        $factoryEventManager->attach($workerEventListeners);

        $factory = $serviceManager->get('FdlGatewayFactory');

        // This is where to hook to WorkerEvent
        // Each listener here is triggered everytime a gateway is created
        // so be careful
        $this->factoryEventManager->trigger(
            GatewayFactoryEvent::PRE_RUN,
            $factory,
            $serviceManager->get('FdlGatewayFactoryEvent')
        );

        // execute the listeners
        $factory->setWorkerEvent($workerEvent);
        $factory->run();

        // retrieve the instantiated tablegateway
        $tableGateway = $factory->getTableGateway();

        //reset the factory and detach attached listeners
        $factory->clearProperties();
        $factoryEventManager->detach($workerEventListeners);

        return $tableGateway;
    }
}
