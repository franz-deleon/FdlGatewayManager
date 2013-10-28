<?php
namespace FdlGatewayManager\Sql;

use FdlGatewayManager\GatewayFactory;

abstract class AbstractSql implements SqlInterface
{
    /**
     * @var \Zend\Db\Sql\SqlInterface
     */
    protected $sql;

    /**
     * @var GatewayFactory
     */
    protected $gatewayFactory;

    /**
     * Retrieve the created feature
     * @param void
     * @return \Zend\Db\Sql\SqlInterface
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return GatewayFactory;
     */
    public function getGatewayFactory()
    {
        return $this->gatewayFactory;
    }

    /**
     * @param GatewayFactory $factory;
     */
    public function setGatewayFactory(GatewayFactory $factory)
    {
        $this->gatewayFactory = $factory;
        return $this;
    }
}
