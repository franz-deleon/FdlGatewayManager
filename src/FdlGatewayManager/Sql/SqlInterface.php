<?php
namespace FdlGatewayManager\Sql;

interface SqlInterface
{
    /**
     * Retrieve the created sql
     * @param void
     * @return \Zend\Db\Sql\SqlInterface
     */
    public function getSql();

    /**
     * Create the feature
     * @param void
     * @return null
     */
    public function create();
}
