<?php
namespace LocGatewayManager;

use Zend\Db;

class GatewayFactory extends AbstractGatewayFactory
{
    /**
     * @var \Zend\Db\Adapter\Adapter
     */
    protected $adapter;

    /**
     * @var resource
     */
    protected $entity;

    /**
     * @var \LocGatewayManager\Feature\AbstractFeature
     */
    protected $feature;

    /**
     * @var \LocGatewayManager\ResultSet\AbstractResultSet
     */
    protected $resultSet;

    /**
     * Tablename
     * @var string
     */
    protected $table;

    /**
     * @var \Zend\Db\TableGateway\TableGateway
     */
    protected $tableGateway;

    /**
     * @var \LocGatewayManager\GatewayWorker
     */
    protected $gatewayWorker;

    /**
     * Create the tableGateway according to set worker property
     * @param GatewayWorker $worker
     * @return null
     */
    public function __construct(GatewayWorker $worker)
    {
        $this->gatewayWorker = $worker;
    }

    /**
     * Run the factory
     * @param void
     * @return null
     */
    public function run()
    {
        $worker = $this->getWorker();

        $adapterKeyName = $worker->getAdapterKeyName();
        $entityName     = $worker->getEntityName();
        $resultSetName  = $worker->getResultSetName();
        $featureName    = $worker->getFeatureName();
        $tableName      = $worker->getTableName();

        $adapter = $this->initAdapter($adapterKeyName);
        $entity  = $this->initEntity($entityName, $adapter);
        $table   = $this->initTable($tableName, $entity, $adapter);
        $feature   = $this->initFeature($featureName);
        $resultSet = $this->initResultSet($resultSetName);

        $this->setAdapter($adapter);
        $this->setEntity($entity);
        $this->setTable($table);

        // initialize feature
        if (isset($feature)) {
            if ($feature instanceof Feature\FeatureInterface) {
                $feature->setLocGatewayFactory($this)->create();
                $this->setFeature($feature->getFeature());
            } else {
                $this->setFeature($feature);
            }
        }

        // initialize resultset
        if (isset($resultSet)) {
            if ($resultSet instanceof ResultSet\ResultSetInterface) {
                $resultSet->setLocGatewayFactory($this)->create();
                $this->setResultSet($resultSet->getResultSet());
            } else {
                $this->setResultSet($resultSet);
            }
        }

        $this->tableGateway = $worker->assemble($this);
    }

    /**
     * Returns the gateway worker
     * @return \LocGatewayManager\GatewayWorker
     */
    public function getWorker()
    {
        return $this->gatewayWorker;
    }

    /**
     * Returns created tablegateway
     * @return \Zend\Db\TableGateway\TableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param \Zend\Db\Adapter\Adapter $adapter
     * @return \LocGatewayManager\GatewayFactory
     */
    public function setAdapter(\Zend\Db\Adapter\Adapter $adapter)
    {
        $this->adapter = $adapter;
        return $this;
    }

    /**
     * @param void
     * @return Object Entity object
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set the entity object
     * @param object $entity
     * @return \LocGatewayManager\GatewayFactory
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return \LocGatewayManager\Feature\AbstractFeature
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param unknown $feature
     * @return Db\TableGateway\Feature\AbstractFeature
     */
    public function setFeature(Db\TableGateway\Feature\AbstractFeature $feature)
    {
        $this->feature = $feature;
        return $this;
    }

    /**
     * @return \LocGatewayManager\ResultSet\AbstractResultSet
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }

    /**
     * @param Db\ResultSet\ResultSetInterface $resultSet
     */
    public function setResultSet(Db\ResultSet\ResultSetInterface $resultSet)
    {
        $this->resultSet = $resultSet;
    }

    /**
     * @return object table object
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param object $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }
}
