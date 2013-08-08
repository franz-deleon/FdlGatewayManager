<?php
namespace LocGatewayManager;

use Zend\Db\TableGateway\TableGateway;

class GatewayWorker implements WorkerInterface
{
    /**
     * @var string
     */
    protected $adapterKeyName;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var string
     */
    protected $featureName;

    /**
     * @var string
     */
    protected $resultSetName;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * The worker assembles the table gateway
     * @param string $entityName Table Entity
     * @return TableGateway;
     */
    public function assemble(GatewayFactory $factory)
    {
        // main assembling starts now
        $adapter   = $factory->getAdapter();
        $feature   = $factory->getFeature();
        $resultSet = $factory->getResultSet();
        $table     = $factory->getTable();

        // assemble
        return new TableGateway($table, $adapter, $feature, $resultSet);
    }

    /**
     * Retrieve the adapter key name
     * @param void
     * @return string
     */
    public function getAdapterKeyName()
    {
        return $this->adapterKeyName;
    }

    /**
     * Set the adapter key name
     * Note: the key is array key on config.
     * @param string $adapterKey
     * @return \LocDb\LocDbManager
     */
    public function setAdapterKeyName($adapterKey)
    {
        $this->adapterKeyName = $adapterKey;
        return $this;
    }

    /**
     * Return the table name
     * @return string
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @param string $entityName Table name
     * @return LocDbManager
     */
    public function setEntityName($entityName)
    {
        $this->entityName = $entityName;
        return $this;
    }

    /**
     * Get the feature name
     * @param void
     * @return string
     */
    public function getFeatureName()
    {
        return $this->featureName;
    }

    /**
     * Set the feature name to use
     * @param string $feature
     * @return \LocDb\LocDbManager
     */
    public function setFeatureName($feature)
    {
        $this->featureName = $feature;
        return $this;
    }

    /**
     * Result set name
     * @param void
     * @return string
     */
    public function getResultSetName()
    {
        return $this->resultSetName;
    }

    /**
     * @param string $resultSet
     * @return \LocDb\LocDbManager
     */
    public function setResultSetName($resultSet)
    {
        $this->resultSetName = $resultSet;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $table
     */
    public function setTableName($table)
    {
        $this->tableName = $table;
    }
}
