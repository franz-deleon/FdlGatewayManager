<?php
namespace FdlGatewayManager\Gateway;

interface TableInterface
{
    /**
     * @return string The tablename from the database
     */
    public function getTableName();
}
