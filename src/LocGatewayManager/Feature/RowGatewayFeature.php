<?php
namespace LocGatewayManager\Feature;

use Zend\Db\TableGateway\Feature;

class RowGatewayFeature extends AbstractFeature
{
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
}
