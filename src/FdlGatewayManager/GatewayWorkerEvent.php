<?php
namespace FdlGatewayManager;

use Zend\EventManager\Event;

class GatewayWorkerEvent extends Event implements WorkerInterface
{
    /**
     * Gateway worker events
     */
    const INIT_ADAPTER                = 'init.Adapter';
    const RESOLVE_TABLE_NAME          = 'resolve.TableName';
    const RESOLVE_TABLE_GATEWAY       = 'resolve.TableGateway';
    const LOAD_FEATURES               = 'load.Features';
    const LOAD_RESULT_SET_PROTOTYPE   = 'load.ResultSetPrototype';
    const LOAD_SQL                    = 'load.Sql';
    const POST_INIT_TABLE_GATEWAY     = 'post.init.TableGateway';

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
     * Sql
     * @param void
     * @return string
     */
    public function getSqlName()
    {
        return $this->getParam('sql');
    }

    /**
     * @param string $sql
     * @return GatewayFactoryEvent
     */
    public function setSqlName($sql)
    {
        $this->setParam('sql', $sql);
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
    public function setTableName($tableName)
    {
        $this->setParam('table-name', $tableName);
        return $this;
    }
}
