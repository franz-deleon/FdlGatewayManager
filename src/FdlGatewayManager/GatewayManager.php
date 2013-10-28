<?php
namespace FdlGatewayManager;

class GatewayManager extends AbstractServiceLocatorAware
{
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

        // attach the events
        $this->loadWorkerEvents();

        // execute the listeners
        $factory = $this->getFactory();
        $factory->run();

        // retrieve the instantiated tablegateway
        $tableGateway = $factory->getTableGateway();

        //reset the factory
        $factory->reset();
        //$factory->setEventManager(null);

        return $tableGateway;
    }

    public function loadWorkerEvents()
    {
        $factoryEventManager  = $this->getFactory()->getEventManager();
        $workerEventListeners = $this->getServiceLocator()->get('FdlGatewayWorkerEventListeners');
        $factoryEventManager->attach($workerEventListeners);
    }

    public function getFactory()
    {
        $factory = $this->getServiceLocator()->get('FdlGatewayFactory');
        return $factory;
    }
}
