<?php
namespace LocGatewayManager\Gateway;

interface TableInterface
{
    /**
     * @return string The tablename from the database
     */
    public function getTableName();
}
