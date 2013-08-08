<?php
namespace LocGatewayManager\Feature;

use LocGatewayManager\GatewayFactory;

abstract class AbstractFeature implements FeatureInterface
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
