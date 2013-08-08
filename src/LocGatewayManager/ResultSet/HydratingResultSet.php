<?php

namespace LocGatewayManager\ResultSet;

use Zend\Db\ResultSet;
use Zend\Stdlib\Hydrator\ClassMethods;

class HydratingResultSet extends AbstractResultSet
{
    /**
     * @var \Zend\Db\ResultSet\HydratingResultSet
     */
    protected $resultSet;

    /**
     * (non-PHPdoc)
     * @see \LocDb\Manager\ResultSet\ResultSetInterface::create()
     */
    public function create()
    {
        $this->resultSet = new ResultSet\HydratingResultSet(
            new ClassMethods,
            $this->getLocDbManager()->getEntity()
        );
    }

    /**
     * (non-PHPdoc)
     * @see \LocDb\Manager\ResultSet\ResultSetInterface::getResultSet()
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }
}
