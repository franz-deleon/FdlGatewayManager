<?php
namespace LocGatewayManager;

class Manager extends AbstractManager
{
    /**
     * @var array Static registry collection of instantiated gateways
     */
    protected static $tableGateways;

    public function factory()
    {
        $worker = $this->getGatewayWorker();
        var_dump($worker);die;
    }

    /**
     * Retrieve retrieve a specific table gateway
     * @param void
     * @return TableGateway
     */
    public function getTableGateway($table, $adapter = null, $feature = null, $resultSet = null)
    {
        $key = $this->tableGatewayUniqueKey($adapter, $feature, $resultSet);
        if (!isset(self::$tableGateways[$table][$key])) {
            throw new Exception\InvalidArgumentException('Table: "'. $table . '" is not created.');
        }

        return self::$tableGateways[$table][$key];
    }

    /**
     * Is there such an existing table gateway?
     * @param string $table
     * @return boolean
     */
    public function isExistingTableGateway($table, $adapter = null, $feature = null, $resultSet = null)
    {
        $key = $this->tableGatewayUniqueKey($adapter, $feature, $resultSet);
        if (empty(self::$tableGateways[$table][$key])) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve all the table gateways
     * @param void
     * @return array
     */
    public function getTableGateways()
    {
        return self::$tableGateways;
    }

    /**
     * Delete a tablegateway
     * @param TableGateway $tableGateway
     * @param string $table
     * @param string $adapter
     * @param string $feature
     * @param string $resultSet
     * @return \LocGatewayManager\Manager
     */
    public function deleteTableGateway(
        TableGateway $tableGateway,
        $table,
        $adapter = null,
        $feature = null,
        $resultSet = null
    ) {
        $key = $this->tableGatewayUniqueKey($adapter, $feature, $resultSet);
        if (isset(self::$tableGateways[$table][$key])) {
            unset(self::$tableGateways[$table][$key]);
        }
        return $this;
    }

    /**
     * Set a table gateway
     * @param TableGateway $tableGateway
     * @param string $table
     * @param string $adapter
     * @param string $feature
     * @param string $resultSet
     * @return \LocGatewayManager\Manager
     */
    public function saveTableGateway(
        TableGateway $tableGateway,
        $table,
        $adapter = null,
        $feature = null,
        $resultSet = null
    ) {
        $key = $this->tableGatewayUniqueKey($adapter, $feature, $resultSet);
        self::$tableGateways[$table][$key] = $tableGateway;
        return $this;
    }

    /**
     * Creates a unique key out of gateway params
     * @param string $adapter
     * @param string $feature
     * @param string $resultSet
     * @return string
     */
    protected function tableGatewayUniqueKey($adapter = null, $feature = null, $resultSet = null)
    {
        $key = array('default');
        $wfilter = new Word\UnderscoreToCamelCase();

        if (isset($adapter)) {
            $adapterKey = $this->getAdapterKeyName() ? $this->getAdapterKeyName() . '-' : '';
            if (is_object($adapter)) {
                $adapter = get_class($adapter);
                $adapter = substr($adapter, (strrpos($adapter, "\\") + 1));
                $key[] = $adapterKey . $adapter;
            } elseif (is_string($adapter)) {
                $key[] = $adapterKey . $wfilter->filter($adapter);
            }
        }
        if (isset($feature)) {
            if (is_object($feature)) {
                $feature = get_class($feature);
                $feature = substr($feature, (strrpos($feature, "\\") + 1));
                $key[] = $feature;
            } elseif (is_string($feature)) {
                $key[] = $wfilter->filter($feature);
            }
        }
        if (isset($resultSet)) {
            if (is_object($resultSet)) {
                $resultSet = get_class($resultSet);
                $key[] = substr($resultSet, (strrpos($resultSet, "\\") + 1));
            } elseif (is_string($resultSet)) {
                $key[] = $wfilter->filter($resultSet);
            }
        }

        if (count($key) > 1) {
            array_shift($key);
        }

        return strtolower(implode('_', $key));
    }
}
