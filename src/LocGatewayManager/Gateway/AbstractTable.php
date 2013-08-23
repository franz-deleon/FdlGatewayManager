<?php
namespace LocGatewayManager\Gateway;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;

abstract class AbstractTable implements TableInterface
{
    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var AbstractTableGateway
     */
    protected $tableGateway;

    /**
     * Dont let anyone create a contructor because
     * we are initializing this class in module.php without args
     */
    final public function __construct()
    {
        $this->init();
    }

    /**
     * Forward method calls to the table gateway object
     * @param string $name
     * @param array $args
     * @return TableGateway
     */
    public function __call($name, $args)
    {
        $tableGateway = $this->getTableGateway();
        if (isset($tableGateway)) {
            if (method_exists($tableGateway, $name)) {
                return call_user_func_array(array($tableGateway, $name), $args);
            }
        }
    }

    /**
     * Use for overriding and initializing
     * @param void
     * @return mixed
     */
    public function init()
    {
        // left blank intentionally.
    }

    /**
     * Tables primary key if any
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * (non-PHPdoc)
     * @see \LocGatewayManager\Gateway\TableInterface::getTableName()
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @return \Zend\Db\TableGateway\AbstractTableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * @param AbstractTableGateway $tableGateway
     */
    public function setTableGateway(AbstractTableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
        return $this;
    }
}
