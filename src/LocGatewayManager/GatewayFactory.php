<?php
namespace LocGatewayManager;

use Zend\Db;

class GatewayFactory
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
     * @var \Zend\Db\TableGateway\AbstractTableGateway
     */
    protected $tableGateway;

    /**
     * @var string
     */
    protected $tableGatewayTarget;

    /**
     * @var \LocGatewayManager\GatewayWorker
     */
    protected $gatewayWorker;

    /**
     * @var \LocGatewayManager\GatewayFactoryProcessor
     */
    protected $factoryUtilities;


    public function __construct(GatewayFactoryUtilities $utilities)
    {
        $this->factoryUtilities = $utilities;
    }

    /**
     * Run the factory
     * @param void
     * @return null
     */
    public function run()
    {
        $worker = $this->getWorker();
        $processor = $this->factoryUtilities;

        if (isset($worker) && $worker instanceof GatewayWorker) {
            $adapterKeyName = $worker->getAdapterKeyName();
            $entityName     = $worker->getEntityName();
            $resultSetName  = $worker->getResultSetName();
            $featureName    = $worker->getFeatureName();
            $tableName      = $worker->getTableName();
            $tableGatewayName = $worker->getTableGatewayName();

            $processor->setAdapterKey($adapterKeyName);
            $adapter = $processor->initAdapter($adapterKeyName);
            $entity  = $processor->initEntity($entityName, $adapter);

            $table   = $processor->getTable($tableName, $entity, $adapter);
            $tableGatewayTarget = $processor->getTableGatewayTarget($tableGatewayName, $entity);

            $feature   = $processor->initFeature($featureName);
            $resultSet = $processor->initResultSet($resultSetName);

            $this->setAdapter($adapter);
            $this->setEntity($entity);
            $this->setTable($table);
            $this->setTableGatewayTarget($tableGatewayTarget);

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

            $worker->assemble($this);
        } else {
            throw new Exception\ClassNotExistException('There is no worker defined');
        }
    }

    /**
     * @return \Zend\Db\TableGateway\AbstractTableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * @param Db\TableGateway\AbstractTableGateway $tableGateway
     * @return \LocGatewayManager\GatewayFactory
     */
    public function setTableGateway($tableGateway)
    {
        if (!$tableGateway instanceof Db\TableGateway\AbstractTableGateway
            && !$tableGateway instanceof Gateway\AbstractTable
        ) {
            throw new Exception\InvalidArgumentException('Class needs to be an instance of Gateway\AbstractTable');
        }
        $this->tableGateway = $tableGateway;
        return $this;
    }

    /**
     * @return string
     */
    public function getTableGatewayTarget()
    {
        return $this->tableGatewayTarget;
    }

    /**
     * @param string $tableGatewayString
     */
    public function setTableGatewayTarget($tableGatewayTarget)
    {
        $this->tableGatewayTarget = $tableGatewayTarget;
        return $this;
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
     * @param GatewayWorker $worker
     * @return \LocGatewayManager\GatewayFactory
     */
    public function setWorker(GatewayWorker $worker = null)
    {
        $this->gatewayWorker = $worker;
        return $this;
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
    public function setAdapter(Db\Adapter\Adapter $adapter)
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
     * @param Db\TableGateway\Feature\AbstractFeature $feature
     * @return \LocGatewayManager\GatewayFactory
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

    /**
     * Reset the gateway worker
     * @param void
     */
    public function reset()
    {
        $properties = get_object_vars($this);
        while (list($key) = each($properties)) {
            if ($key != 'serviceLocator' && $key != 'factoryUtilities') {
                $this->{$key} = null;
            }
        }
    }
}
