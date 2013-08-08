<?php
namespace LocGatewayManager\ResultSet;

use LocGatewayManager\GatewayFactory;

abstract class AbstractResultSet implements ResultSetInterface
{
    /**
     * @var GatewayFactory
     */
    protected $gatewayFactory;

    /**
     * @return GatewayFactory;
     */
    public function getLocGatewayFactory()
    {
        return $this->gatewayFactory;
    }

    /**
     * @param GatewayFactory $factory;
     */
    public function setLocGatewayFactory(GatewayFactory $factory)
    {
        $this->gatewayFactory = $factory;
        return $this;
    }
}
