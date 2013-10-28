<?php
namespace FdlGatewayManager\Feature;

use Zend\Db\TableGateway\Feature;

class RowGatewayFeature extends AbstractFeature
{
    /**
     * (non-PHPdoc)
     * @see \FdlGatewayManager\Feature\FeatureInterface::create()
     */
    public function create()
    {
        $table = $this->getGatewayFactory()->getTableGateway();
        if (isset($table)) {
            $table = new $table();
            if (property_exists($table, 'primaryKey')) {
                $primaryKey = $table->primaryKey;
            } elseif (is_callable(array($table, 'getPrimaryKey')) && $table->getPrimaryKey() !== null) {
                $primaryKey = $table->getPrimaryKey();
            }
        }

        if (isset($primaryKey)) {
            $this->feature = new Feature\RowGatewayFeature($primaryKey);
        } else {
            $this->feature = new Feature\RowGatewayFeature();
        }
    }
}
