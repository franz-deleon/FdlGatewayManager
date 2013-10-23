<?php

namespace FdlGatewayManager\ResultSet;

use Zend\Db\ResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;

class HydratingResultSet extends AbstractResultSet
{
    /**
     * @var \Zend\Db\ResultSet\HydratingResultSet
     */
    protected $resultSet;

    public function create()
    {
        $this->resultSet = new ResultSet\HydratingResultSet(
            new ClassMethods(true),
            $this->getFdlGatewayFactory()->getEntity()
        );
    }

    /**
     * @return \Zend\Db\ResultSet\HydratingResultSet
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }
}
