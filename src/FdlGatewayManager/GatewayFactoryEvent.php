<?php
namespace FdlGatewayManager;

use Zend\EventManager\Event;

class GatewayFactoryEvent extends Event
{
    /**
     * Gateway events
     */
    const INIT_ADAPTER  = 'init.Adapter';
    const LOAD_FEATURES = 'load.Features';
    const LOAD_RESULT_SET_PROTOTYPE = 'load.ResultSetPrototype';
    const LOAD_SQL = 'load.Sql';
    const RESOLVE_TABLE = 'resolve.Table';

    /**
     * Get the adapter key
     * @return string
     */
    public function getAdapterKey()
    {
        return $this->getParam('adapter-key');
    }

    /**
     * Set adapter key
     * @param string
     * @return GatewayFactoryEvent
     */
    public function setAdapterKey($adapterKey)
    {
        $this->setParam('adapter-key', $adapterKey);
        return $this;
    }

    /**
     * Get entity name
     * @param string
     * @return GatewayFactoryEvent
     */
    public function getEntityName()
    {
        return $this->getParam('entity-name');
    }

    /**
     * Set entity name
     *
     * @param string
     * @return GatewayFactoryEvent
     */
    public function setEntityName($entityName)
    {
        $this->setParam('entity-name', $entityName);
        return $this;
    }

    /**
     * Get the feature name
     * @param void
     * @return string
     */
    public function getFeatureName()
    {
        return $this->getParam('feature-name');
    }

    /**
     * Set the feature name to use
     * @param string $feature
     * @return GatewayFactoryEvent
     */
    public function setFeatureName($featureName)
    {
        $this->setParam('feature-name', $featureName);
        return $this;
    }

    /**
     * Result set name
     * @param void
     * @return string
     */
    public function getResultSetPrototypeName()
    {
        return $this->getParam('result-set-protype-name');
    }

    /**
     * @param string $resultSet
     * @return GatewayFactoryEvent
     */
    public function setResultSetPrototypeName($resultSetPrototype)
    {
        $this->setParam('result-set-protype-name', $resultSetPrototype);
        return $this;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->getParam('table-name');
    }

    /**
     * @param string
     * @return GatewayFactoryEvent
     */
    public function setTableName($table)
    {
        $this->setParam('table-name');
        return $this;
    }

    /**
     * @param string
     * @return GatewayFactoryEvent
     */
    public function getTableGatewayName()
    {
        return $this->tableGatewayName;
    }

    /**
     * @param string $tableGatewayName
     */
    public function setTableGatewayName($tableGatewayName)
    {
        $this->tableGatewayName = $tableGatewayName;
        return $this;
    }
}
