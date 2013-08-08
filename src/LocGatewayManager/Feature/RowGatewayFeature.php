<?php
namespace LocGatewayManager\Feature;

use Zend\Db\TableGateway\Feature;

class RowGatewayFeature extends AbstractFeature
{
    /**
     * @var \Zend\Db\TableGateway\Feature\AbstractFeature
     */
    protected $feature;

    /**
     * (non-PHPdoc)
     * @see \LocDb\Manager\AbstractDbManagerAware::create()
     */
    public function create()
    {
        $entity = $this->getLocGatewayFactory()->getEntity();
        if (isset($entity)) {
            $this->feature = new Feature\RowGatewayFeature($entity);
        } else {
            $this->feature = new Feature\RowGatewayFeature();
        }
    }

    /**
     * Retrieve the created feature
     * @param void
     * @return \Zend\Db\TableGateway\Feature\AbstractFeature
     */
    public function getFeature()
    {
        return $this->feature;
    }
}
