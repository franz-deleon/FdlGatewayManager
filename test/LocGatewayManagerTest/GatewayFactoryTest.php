<?php
namespace LocGatewayManagerTest;

use LocGatewayManager;

/**
 * GatewayFactory test case.
 */
class GatewayFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
	 * @var GatewayFactory
	 */
    private $GatewayFactory;

    /**
	 * Prepares the environment before running a test.
	 */
    protected function setUp()
    {
        parent::setUp();
        $this->GatewayFactory = $this->getMockBuilder('LocGatewayManager\GatewayFactory')
                                     ->disableOriginalConstructor();
    }

    /**
	 * Cleans up the environment after running a test.
	 */
    protected function tearDown()
    {
        $this->GatewayFactory = null;
        parent::tearDown();
    }

    /**
	 * Tests GatewayFactory->__construct()
	 * @group Run
	 */
    public function testRun()
    {
        // Gateway Worker stub
        $workerMock = $this->getMockBuilder('\LocGatewayManager\GatewayWorker')
                           ->setMethods(array('getAdapterKeyName', 'getEntityName', 'getResultSetName', 'getFeatureName', 'getTableName', 'assemble'))
                           ->getMock();
        $workerMock->expects($this->any())
                   ->method('getAdapterKeyName')
                   ->will($this->returnValue('Adapterkey'));
        $workerMock->expects($this->any())
                   ->method('getEntityName')
                   ->will($this->returnValue('entityname'));
        $workerMock->expects($this->any())
                   ->method('getResultSetName')
                   ->will($this->returnValue('resultSetName'));
        $workerMock->expects($this->any())
                   ->method('getFeatureName')
                   ->will($this->returnValue('featureName'));
        $workerMock->expects($this->any())
                   ->method('getTableName')
                   ->will($this->returnValue('tableName'));
        $workerMock->expects($this->any())
                   ->method('assemble')
                   ->will($this->returnSelf());

        // zend db feature stub
        $zendFeature = $this->getMockBuilder('\Zend\Db\TableGateway\Feature\RowGatewayFeature')
                              ->disableOriginalConstructor()
                              ->getMock();

        // feature stub
        $featureStub = $this->getMock('LocGatewayManager\Feature\RowGatewayFeature', array('setLocGatewayFactory', 'create', 'getFeature'));
        $featureStub->expects($this->any())
                    ->method('setLocGatewayFactory')
                    ->will($this->returnSelf());
        $featureStub->expects($this->any())
                    ->method('create')
                    ->will($this->returnSelf());
        $featureStub->expects($this->any())
                    ->method('getFeature')
                    ->will($this->returnValue($zendFeature));

        // zend db result set stub
        $zendResultSet = $this->getMockBuilder('\Zend\Db\ResultSet\HydratingResultSet')
                              ->disableOriginalConstructor()
                              ->getMock();

        // resultset stub
        $resultSetStub = $this->getMock('LocGatewayManager\ResultSet\HydratingResultSet', array('setLocGatewayFactory', 'create', 'getResultSet'));
        $resultSetStub->expects($this->any())
                      ->method('setLocGatewayFactory')
                      ->will($this->returnSelf());
        $resultSetStub->expects($this->any())
                      ->method('create')
                      ->will($this->returnSelf());
        $resultSetStub->expects($this->any())
                      ->method('getResultSet')
                      ->will($this->returnValue($zendResultSet));

        // zend db stub
        $zendAdapter = $this->getMockBuilder('\Zend\Db\Adapter\Adapter')
                            ->disableOriginalConstructor()
                            ->getMock();

        // factory mock
        $gatewayFactoryMock = $this->GatewayFactory
                                   ->setMethods(array('initAdapter', 'initEntity', 'initTable', 'initFeature', 'initResultSet', 'setFeature', 'setResultSet'))
                                   ->getMock();
        $gatewayFactoryMock->expects($this->once())
                           ->method('initAdapter')
                           ->will($this->returnValue($zendAdapter));
        $gatewayFactoryMock->expects($this->once())
                           ->method('initEntity')
                           ->with($this->equalTo('entityname'), $this->containsOnlyInstancesOf('\Zend\Db\Adapter\Adapter'))
                           ->will($this->returnSelf());
        $gatewayFactoryMock->expects($this->once())
                           ->method('initTable')
                           ->will($this->returnSelf());
        $gatewayFactoryMock->expects($this->once())
                           ->method('initFeature')
                           ->will($this->returnValue($featureStub));
        $gatewayFactoryMock->expects($this->once())
                           ->method('initResultSet')
                           ->will($this->returnValue($resultSetStub));
        $gatewayFactoryMock->expects($this->once())
                           ->method('setFeature')
                           ->will($this->returnSelf());
        $gatewayFactoryMock->expects($this->once())
                           ->method('setResultSet')
                           ->will($this->returnSelf());

        $gatewayFactoryMock->setWorker($workerMock);
        $gatewayFactoryMock->run();

        // test with feature and result return origin objects
        $gatewayFactoryMock2 = $this->GatewayFactory
                                    ->setMethods(array('initAdapter', 'initEntity', 'initTable', 'initFeature', 'initResultSet', 'setFeature', 'setResultSet'))
                                    ->getMock();
        $gatewayFactoryMock2->expects($this->once())
                            ->method('initAdapter')
                            ->will($this->returnValue($zendAdapter));
        $gatewayFactoryMock2->expects($this->once())
                            ->method('initFeature')
                            ->will($this->returnValue($zendFeature));
        $gatewayFactoryMock2->expects($this->once())
                            ->method('initResultSet')
                            ->will($this->returnValue($zendResultSet));

        $gatewayFactoryMock2->setWorker($workerMock);
        $gatewayFactoryMock2->run();
    }

    /**
     * @expectedException LocGatewayManager\Exception\ClassNotExistException
     * @group Run
     */
    public function testRunWithNoWorker()
    {
        $gatewayFactoryMock = $this->GatewayFactory
                                   ->setMethods(null)
                                   ->getMock();
        $gatewayFactoryMock->run();
    }

    /**
     * @group CreateTableGateway
     */
    public function testGetTableGateway()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();
        $this->assertNull($tableGateway->getTableGateway());
    }

    /**
     * @group CreateTableGateway
     */
    public function testSetTableGateway()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();
        $tableGateway->setTableGateway($this->getMock('\Zend\Db\TableGateway\TableGateway', array(), array(), '', false));
    }

    /**
	 * Tests GatewayFactory->getAdapter()
	 * @group adapter
	 */
    public function testGetAdapter()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();

        $this->assertNull($tableGateway->getAdapter());
    }

    /**
	 * Tests GatewayFactory->setAdapter()
	 * @group adapter
	 */
    public function testSetAdapter()
    {
        // zend db stub
        $zendAdapter = $this->getMockBuilder('\Zend\Db\Adapter\Adapter')
                            ->disableOriginalConstructor()
                            ->getMock();

        $tableGateway = $this->GatewayFactory
                             ->setMethods(null)
                             ->getMock();

        $tableGateway->setAdapter($zendAdapter);

        $this->assertInstanceOf('\Zend\Db\Adapter\Adapter', $tableGateway->getAdapter());
    }

    /**
	 * Tests GatewayFactory->getEntity()
	 * @group entity
	 */
    public function testGetEntity()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();

        $this->assertNull($tableGateway->getEntity());
    }

    /**
	 * Tests GatewayFactory->setEntity()
	 * @group entity
	 */
    public function testSetEntity()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();
        $tableGateway->setEntity('xxEntityxx');

        $this->assertEquals('xxEntityxx', $tableGateway->getEntity());
    }

    /**
	 * Tests GatewayFactory->getFeature()
	 * @group feature
	 */
    public function testGetFeature()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();

        $this->assertNull($tableGateway->getFeature());
    }

    /**
	 * Tests GatewayFactory->setFeature()
	 * @group feature
	 */
    public function testSetFeature()
    {
        // feature db stub
        $feature = $this->getMockBuilder('\Zend\Db\TableGateway\Feature\RowGatewayFeature')
                        ->disableOriginalConstructor()
                        ->getMock();
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();
        $tableGateway->setFeature($feature);

        $this->assertInstanceOf('\Zend\Db\TableGateway\Feature\RowGatewayFeature', $tableGateway->getFeature());
    }

    /**
	 * Tests GatewayFactory->getResultSet()
	 * @group ResultSet
	 */
    public function testGetResultSet()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();

        $this->assertNull($tableGateway->getResultSet());
    }

    /**
	 * Tests GatewayFactory->setResultSet()
	 * @group ResultSet
	 */
    public function testSetResultSet()
    {
        // resultSet stub
        $resultSet = $this->getMockBuilder('\Zend\Db\ResultSet\HydratingResultSet')
                          ->disableOriginalConstructor()
                          ->getMock();
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();
        $tableGateway->setResultSet($resultSet);

        $this->assertInstanceOf('\Zend\Db\ResultSet\HydratingResultSet', $tableGateway->getResultSet());
    }

    /**
	 * Tests GatewayFactory->getTable()
	 * @group Table
	 */
    public function testGetTable()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();

        $this->assertNull($tableGateway->getTable());
    }

    /**
	 * Tests GatewayFactory->setTable()
	 * @group Table
	 */
    public function testSetTable()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();
        $tableGateway->setTable('zzTABLEzz');

        $this->assertEquals('zzTABLEzz', $tableGateway->getTable());
    }

    /**
     * @group Reset
     */
    public function testReset()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();
        $tableGateway->setTable('sTABLEs');
        $this->assertEquals('sTABLEs', $tableGateway->getTable());
        $tableGateway->reset();
        $this->assertNull($tableGateway->getTable());
    }
}
