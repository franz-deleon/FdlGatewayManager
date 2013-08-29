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
        $table = $this->getLocGatewayFactory()->getTableGatewayTarget();
        if (isset($table)) {
            $table = new $table();
            if (property_exists($table, 'primaryKey')) {
                $key = $table->primaryKey;
            } elseif (is_callable(array($table, 'getPrimaryKey')) && $table->getPrimaryKey() !== null) {
                $key = $table->getPrimaryKey();
            }
        }

        if (isset($key)) {
            $this->feature = new Feature\RowGatewayFeature($key);
        } else {
            $this->feature = new Feature\RowGatewayFeature();
        }
    }
}
