<?php

namespace FdlGatewayManager\ResultSet;

use Zend\Db\ResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;

class HydratingResultSet extends AbstractResultSet
{
    public function create()
    {
        $this->resultSetPrototype = new ResultSet\HydratingResultSet(
            new ClassMethods(true),
            $this->getGatewayFactory()->getEntity()
        );
    }
}
