<?php
namespace FdlGatewayManager;

use Zend\Db;

class GatewayFactory extends AbstractServiceLocatorAware
{
    /**
     * @var \Zend\Db\Adapter\AdapterInterface
     */
    protected $adapter;

    /**
     * @var resource
     */
    protected $entity;

    /**
     * @var \Zend\Db\Feature\AbstractFeature|Feature\FeatureSet|Feature\AbstractFeature[] $features
     */
    protected $feature;

    /**
     * @var \Zend\Db\ResultSet\ResultSetInterface|ResultSet\ResultSetInterface
     */
    protected $resultSetPrototype;

    /**
     * @var \Zend\Db\Sql\SqlInterface
     */
    protected $sql;

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
     * @var \FdlGatewayManager\GatewayWorker
     */
    protected $gatewayWorkerEvent;

    /**
     * Run the factory
     * @param void
     * @return null
     */
    public function run()
    {
        $workerEvent  = $this->getWorkerEvent();
        $eventManager = $this->getEventManager();

        if (isset($workerEvent) && $workerEvent instanceof WorkerInterface) {
            // load the adapter
            $eventManager->trigger(GatewayWorkerEvent::INIT_ADAPTER, $this, $workerEvent);

            // resolve the table name
            $eventManager->trigger(GatewayWorkerEvent::RESOLVE_TABLE_NAME, $this, $workerEvent);

            // load the features
            $eventManager->trigger(GatewayWorkerEvent::LOAD_FEATURES, $this, $workerEvent);

            // load the result set prototype
            $eventManager->trigger(GatewayWorkerEvent::LOAD_RESULT_SET_PROTOTYPE, $this, $workerEvent);

            // load the sql
            $eventManager->trigger(GatewayWorkerEvent::LOAD_SQL, $this, $workerEvent);

            // Post initialization. Create the actual TableGateway
            $eventManager->trigger(GatewayWorkerEvent::POST_INIT_TABLE_GATEWAY, $this, $workerEvent);
        } else {
            throw new Exception\ClassNotExistException('There is no worker event');
        }

        // trigger the post run
        $eventManager->trigger(
            GatewayFactoryEvent::POST_RUN,
            $this,
            $this->getServiceLocator()->get('FdlGatewayFactoryEvent')
        );
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
     * @return \FdlGatewayManager\GatewayFactory
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
     * Returns the gateway worker
     * @return \FdlGatewayManager\GatewayWorker
     */
    public function getWorkerEvent()
    {
        return $this->gatewayWorkerEvent;
    }

    /**
     * @param GatewayWorker $workerEvent
     * @return \FdlGatewayManager\GatewayFactory
     */
    public function setWorkerEvent(GatewayWorkerEvent $workerEvent = null)
    {
        $this->gatewayWorkerEvent = $workerEvent;
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
     * @return \FdlGatewayManager\GatewayFactory
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
     * @return \FdlGatewayManager\GatewayFactory
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @return \FdlGatewayManager\Feature\AbstractFeature
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * @param Db\TableGateway\Feature\AbstractFeature $feature
     * @return \FdlGatewayManager\GatewayFactory
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;
        return $this;
    }

    /**
     * @return \FdlGatewayManager\ResultSet\AbstractResultSet
     */
    public function getResultSetPrototype()
    {
        return $this->resultSetPrototype;
    }

    /**
     * @param Db\ResultSet\ResultSetInterface $resultSetPrototype
     */
    public function setResultSetPrototype(Db\ResultSet\ResultSetInterface $resultSetPrototype)
    {
        $this->resultSetPrototype = $resultSetPrototype;
        return $this;
    }

    /**
     * @return \Zend\Db\Sql\SqlInterface
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @param Db\Sql\SqlInterface $sql
     * @return \FdlGatewayManager\GatewayFactory
     */
    public function setSql(Db\Sql\SqlInterface $sql)
    {
        $this->sql = $sql;
        return $this;
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
        return $this;
    }

    /**
     * Reset the gateway worker
     * @param void
     */
    public function clearProperties()
    {
        $properties = get_object_vars($this);
        while (list($key) = each($properties)) {
            if ($key != 'serviceLocator' && $key != 'eventManager') {
                $this->{$key} = null;
            }
        }
    }
}
