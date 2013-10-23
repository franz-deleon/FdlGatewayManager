<?php
namespace FdlGatewayManager\Feature;

interface FeatureInterface
{
    /**
     * Retrieve the created feature
     * @param void
     * @return \Zend\Db\TableGateway\Feature\AbstractFeature
     */
    public function getFeature();

    /**
     * Create the feature
     * @param void
     * @return null
     */
    public function create();
}