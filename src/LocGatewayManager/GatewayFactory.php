<?php
namespace LocGatewayManager;

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
     * Create the tableGateway according to set worker property
     * @param GatewayWorker $worker
     * @return null
     */
    public function __construct(GatewayWorker $worker)
    {
        $adapterKeyName = $worker->getAdapterKeyName();
        $entityName     = $worker->getEntityName();
        $resultSetName  = $worker->getResultSetName();
        $featureName    = $worker->getFeatureName();
        $tableName      = $worker->getTableName();

        $adapter = $this->createAdapter($adapterKeyName);
        $entity  = $this->createEntity($entityName, $adapter);
        $table   = $this->createTable($tableName, $entity);
        $feature   = $this->createFeature($featureName);
        $resultSet = $this->createResultSet($resultSetName);


        $this->setAdapter($adapter);
        $this->setEntity($entity);
        $this->setTable($table);

        // initialize feature
        if (isset($feature)) {
            $feature->setLocGatewayFactory($this)->create();
            $this->setFeature($feature);
        }

        // initialize resultset
        if (isset($resultSet)) {
            $resultSet->setLocGatewayFactory($this)->create();
            $this->setResultSet($resultSet);
        }

        $this->tableGateway = $worker->assemble($this);
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
    public function setAdapter($adapter)
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
     * @return \LocGatewayManager\Feature\AbstractFeature
     */
    public function setFeature(Feature\FeatureInterface $feature)
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
     * @param ResultSet\ResultSetInterface $resultSet
     */
    public function setResultSet(ResultSet\ResultSetInterface $resultSet)
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
