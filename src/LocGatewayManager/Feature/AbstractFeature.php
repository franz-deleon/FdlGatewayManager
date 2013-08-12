<?php
namespace LocGatewayManager\Feature;

use LocGatewayManager\GatewayFactory;

abstract class AbstractFeature implements FeatureInterface
{
    /**
     * @var \Zend\Db\TableGateway\Feature\AbstractFeature
     */
    protected $feature;

    /**
     * @var GatewayFactory
     */
    protected $gatewayFactory;

    /**
     * Retrieve the created feature
     * @param void
     * @return \Zend\Db\TableGateway\Feature\AbstractFeature
     */
    public function getFeature()
    {
        return $this->feature;
    }

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
