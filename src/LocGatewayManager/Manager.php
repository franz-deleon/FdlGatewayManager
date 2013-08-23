<?php
namespace LocGatewayManager;

use Zend\Db\TableGateway;

class Manager extends AbstractManager
{
    /**
     * Static registry collection of instantiated gateways
     * @var array
     */
    protected static $tableGateways = array();

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
    public function factory(array $params, $index)
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
        $this->saveGateway($tableGateway, $index);

        //reset the factory
        $factory->reset();

        return $tableGateway;
    }

    /**
     * @param TableGateway\AbstractTableGateway $tableGateway
     * @param string $index
     * @throws Exception\ErrorException
     */
    public function saveGateway($tableGateway, $index)
    {
        if (!isset(self::$tableGateways[$index])) {
            self::$tableGateways[$index] = $tableGateway;
        } else {
            throw new Exception\ErrorException(
                'Index: "' . $index . '" is already defined. You may want to use get(' . $index . ') instead.'
            );
        }
    }

    /**
     * @param string $index
     * @return boolean
     */
    public function deleteGateway($index)
    {
        if (isset(self::$tableGateways[$index])) {
            unset(self::$tableGateways[$index]);
            return true;
        }
        return false;
    }

    /**
     * @param string $index
     * @throws Exception\InvalidArgumentException
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function get($index)
    {
        if (isset(self::$tableGateways[$index])) {
            return self::$tableGateways[$index];
        } else {
            throw new Exception\InvalidArgumentException("Gateway index: " . $index . " is not defined yet.");
        }
    }

    /**
     * @return array
     */
    public function getGateways()
    {
        return self::$tableGateways;
    }

    /**
     * @param string $index
     * @return boolean
     */
    public function gatewayExist($index)
    {
        if (isset(self::$tableGateways[$index])) {
            return true;
        }
        return false;
    }
}
