<?php
namespace LocGatewayManager;

use Zend\Db\TableGateway;

class Manager extends AbstractManager
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
        $worker  = $this->getGatewayWorker();

        if (isset($params['adapter_key_name'])) {
            $worker->setAdapterKeyName($params['adapter_key_name']);
        }
        if (isset($params['table_name'])) {
            $worker->setTableName($params['table_name']);
        }
        if (isset($params['entity_name'])) {
            $worker->setEntityName($params['entity_name']);
        }
        if (isset($params['feature_name'])) {
            $worker->setFeatureName($params['feature_name']);
        }
        if (isset($params['result_set_name'])) {
            $worker->setResultSetName($params['result_set_name']);
        }

        // is a Service Manager factory which injects the worker
        $factory = $this->getGatewayFactory();
        $factory->setWorker($worker)
                ->run();

        $tableGateway = $factory->getTableGateway();

        //reset the factory
        $factory->reset();

        return $tableGateway;
    }
}
