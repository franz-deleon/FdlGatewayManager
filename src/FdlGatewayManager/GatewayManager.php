<?php
namespace FdlGatewayManager;

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
        $event   = $this->getServiceLocator()->get('FdlGatewayWorkerEvent');

        if (isset($params['adapter_key_name'])) {
            $event->setAdapterKey($params['adapter_key_name']);
        }
        if (isset($params['entity_name'])) {
            $event->setEntityName($params['entity_name']);
        }
        if (isset($params['feature_name'])) {
            $event->setFeatureName($params['feature_name']);
        }
        if (isset($params['result_set_name'])) {
            $event->setResultSetPrototypeName($params['result_set_name']);
        }
        if (isset($params['table_name'])) {
            $event->setTableName($params['table_name']);
        }

        // set the worker
        $factory = $this->getFactory();

        // load the adapter
        $this->loadWorkerEvents();

        $factory->run();
        $tableGateway = $factory->getTableGateway();

        //reset the factory
        $factory->reset();

        return $tableGateway;
    }

    public function loadWorkerEvents()
    {
        $factoryEventManager = $this->getServiceLocator()->get('FdlGatewayFactory')->getEventManager();
        $workerEventListeners = $this->getServiceLocator()->get('FdlGatewayWorkerEventListeners');
        $factoryEventManager->attach($workerEventListeners);
    }

    public function getFactory()
    {
        return $this->getServiceLocator()->get('FdlGatewayFactory');
    }
}
