<?php

namespace FdlGatewayManager\ResultSet;

use Zend\Db\ResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;

class HydratingResultSet extends AbstractResultSet
{
    /**
     * @var \Zend\Db\ResultSet\HydratingResultSet
     */
    protected $resultSetPrototype;

    public function create()
    {
        $this->resultSetPrototype = new ResultSet\HydratingResultSet(
            new ClassMethods(true),
            $this->getGatewayFactory()->getEntity()
        );
    }

    /**
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    public function getResultSetPrototype()
    {
        return $this->resultSetPrototype;
    }
}
