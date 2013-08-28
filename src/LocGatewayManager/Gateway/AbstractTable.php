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


    /**
     * Filter a set of array data and return filtered results
     *
     * USAGE:
     * $this->filter(array(
     *     'username' => 'xxx',
     *     'password' => 'yyy',
     * ));
     *
     * @param array $data
     * @return multitype:
     */
    public function filter($data = array())
    {
        $filter = $this->getFilter();
        if (null !== $filter) {
            $filter->makeRequired(array_flip($data));

            $factory = new InputFilterFactory();
            $inputFilter = $factory->createInputFilter($filter);

            $inputFilter->setData($data);
            return $inputFilter->getValues();
        }
    }

    /**
     * Fetch all database rows
     * @param string $order
     * @return Iteratable
     */
    public function getAll($order = 'ASC')
    {
        $table  = $this->getTableGateway();
        $select = $table->getSql()->select();

        if (($primaryKey = $this->getPrimaryKey()) !== null) {
            $order = strtoupper($order);
            $select->order("{$primaryKey} {$order}");
        }
        $result = $table->selectWith($select);

        return $result;
    }

    /**
     * You want to...
     * SELECT * FROM TABLE WHERE USERNAME = SOMEVALUE
     *
     * USAGE:
     * $this->getWhere(array(
     *     array(
     *         'clause'    => 'USERNAME = ?',
     *         'value'     => $username,
     *         'predicate' => 'AND'
     *     )
     * ))
     *
     * OR passing one:
     *
     * $this->getWhere(array(
     *     'USERNAME = ?',
     *     $username,
     *     'AND'
     * ))
     * @param array $where
     */
    public function getWhere(array $where)
    {
        $table  = $this->getTableGateway();
        $select = $table->getSql()->select();

        if (count($where) > 0 && !is_array($where[0])) {
            $where = array($where);
        }

        foreach ($where as $values) {
            $clause    = isset($values[0]) ? $values[0] : '';
            $value     = isset($values[1]) ? $values[1] : '';
            $predicate = isset($values[2]) ? $values[2] : \Zend\Db\Sql\Predicate\Predicate::OP_AND;
            $select->where(array($clause => $value), $predicate);
        }

        $result = $table->selectWith($select);
        $selectString = @$table->getSql()->getSqlStringForSqlObject($select);
        return $result;
    }
}
