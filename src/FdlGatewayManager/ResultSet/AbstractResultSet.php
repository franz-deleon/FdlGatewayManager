<?php
namespace FdlGatewayManager\ResultSet;

use FdlGatewayManager\GatewayFactory;

abstract class AbstractResultSet implements ResultSetInterface
{
    /**
     * @var GatewayFactory
     */
    protected $gatewayFactory;

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
