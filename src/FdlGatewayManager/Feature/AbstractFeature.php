<?php
namespace FdlGatewayManager\Feature;

use FdlGatewayManager\GatewayFactory;

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
