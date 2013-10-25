<?php
namespace FdlGatewayManager\ResultSet;

interface ResultSetInterface
{
    /**
     * Create the feature
     * @param void
     * @return null
     */
    public function create();

    /**
     * Retrieve the created feature
     * @param void
     * @return \Zend\Db\ResultSet\AbstractResultSet
     */
    public function getResultSetPrototype();
}
