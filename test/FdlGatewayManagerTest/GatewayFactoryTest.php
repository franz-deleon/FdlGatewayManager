<?php
namespace FdlGatewayManagerTest;

use FdlGatewayManager;

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
        $this->GatewayFactory = $this->getMockBuilder('FdlGatewayManager\GatewayFactory')
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
        $workerStub = $this->getMockBuilder('\FdlGatewayManager\GatewayWorker')
                           ->setMethods(array('getAdapterKeyName', 'getEntityName', 'getResultSetName', 'getFeatureName', 'getTableName', 'assemble'))
                           ->getMock();
        $workerStub->expects($this->any())
                   ->method('getAdapterKeyName')
                   ->will($this->returnValue('Adapterkey'));
        $workerStub->expects($this->any())
                   ->method('getEntityName')
                   ->will($this->returnValue('entityname'));
        $workerStub->expects($this->any())
                   ->method('getResultSetName')
                   ->will($this->returnValue('resultSetName'));
        $workerStub->expects($this->any())
                   ->method('getFeatureName')
                   ->will($this->returnValue('featureName'));
        $workerStub->expects($this->any())
                   ->method('getTableName')
                   ->will($this->returnValue('tableName'));
        $workerStub->expects($this->any())
                   ->method('assemble')
                   ->will($this->returnSelf());

        // zend db feature stub
        $zendFeature = $this->getMockBuilder('\Zend\Db\TableGateway\Feature\RowGatewayFeature')
                              ->disableOriginalConstructor()
                              ->getMock();

        // feature stub
        $featureStub = $this->getMock('FdlGatewayManager\Feature\RowGatewayFeature', array('setFdlGatewayFactory', 'create', 'getFeature'));
        $featureStub->expects($this->any())
                    ->method('setFdlGatewayFactory')
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
        $resultSetStub = $this->getMock('FdlGatewayManager\ResultSet\HydratingResultSet', array('setFdlGatewayFactory', 'create', 'getResultSet'));
        $resultSetStub->expects($this->any())
                      ->method('setFdlGatewayFactory')
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
        $factoryUtilities = $this->getMockBuilder('FdlGatewayManager\GatewayFactoryUtilities')
                                   ->setMethods(array('initAdapter', 'initEntity', 'getTable', 'getTableGatewayProxy', 'initFeature', 'initResultSet', 'setFeature', 'setResultSet'))
                                   ->getMock();
        $factoryUtilities->expects($this->once())
                           ->method('initAdapter')
                           ->will($this->returnValue($zendAdapter));
        $factoryUtilities->expects($this->once())
                           ->method('initEntity')
                           ->with($this->equalTo('entityname'), $this->containsOnlyInstancesOf('\Zend\Db\Adapter\Adapter'))
                           ->will($this->returnSelf());
        $factoryUtilities->expects($this->once())
                           ->method('initFeature')
                           ->will($this->returnValue($featureStub));
        $factoryUtilities->expects($this->once())
                           ->method('initResultSet')
                           ->will($this->returnValue($resultSetStub));

        $gatewayFactoryMock = $this->GatewayFactory->setMethods(null)->getMock();
        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactoryUtilities', $factoryUtilities);

        $gatewayFactoryMock->setServiceLocator($serviceManager);
        $gatewayFactoryMock->setWorker($workerStub);
        $gatewayFactoryMock->run();
    }

    /**
	 * Tests GatewayFactory->__construct()
	 * @group Run2
	 */
    public function testWithFeatureAndResultReturnsOriginalObjects()
    {
        // Gateway Worker stub
        $workerStub = $this->getMockBuilder('\FdlGatewayManager\GatewayWorker')
                           ->setMethods(array('getAdapterKeyName', 'getEntityName', 'getResultSetName', 'getFeatureName', 'getTableName', 'assemble'))
                           ->getMock();
        $workerStub->expects($this->any())
                   ->method('getAdapterKeyName')
                   ->will($this->returnValue('Adapterkey'));
        $workerStub->expects($this->any())
                   ->method('getEntityName')
                   ->will($this->returnValue('entityname'));
        $workerStub->expects($this->any())
                   ->method('getResultSetName')
                   ->will($this->returnValue('resultSetName'));
        $workerStub->expects($this->any())
                   ->method('getFeatureName')
                   ->will($this->returnValue('featureName'));
        $workerStub->expects($this->any())
                   ->method('getTableName')
                   ->will($this->returnValue('tableName'));
        $workerStub->expects($this->any())
                   ->method('assemble')
                   ->will($this->returnSelf());

        // zend db feature stub
        $zendFeature = $this->getMockBuilder('\Zend\Db\TableGateway\Feature\RowGatewayFeature')
                              ->disableOriginalConstructor()
                              ->getMock();

        // feature stub
        $featureStub = $this->getMock('FdlGatewayManager\Feature\RowGatewayFeature', array('setFdlGatewayFactory', 'create', 'getFeature'));
        $featureStub->expects($this->any())
                    ->method('setFdlGatewayFactory')
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

        // zend db stub
        $zendAdapter = $this->getMockBuilder('\Zend\Db\Adapter\Adapter')
                            ->disableOriginalConstructor()
                            ->getMock();

        /**************** test with feature and result return origin objects ****************/
        $factoryUtilities = $this->getMockBuilder('FdlGatewayManager\GatewayFactoryUtilities')
                                  ->setMethods(array('initAdapter', 'initEntity', 'getTable', 'getTableGatewayProxy', 'initFeature', 'initResultSet', 'setFeature', 'setResultSet', 'setAdapterKey'))
                                  ->getMock();
        $factoryUtilities->expects($this->once())
                          ->method('initAdapter')
                          ->will($this->returnValue($zendAdapter));
        $factoryUtilities->expects($this->once())
                          ->method('initFeature')
                          ->will($this->returnValue($zendFeature));
        $factoryUtilities->expects($this->once())
                          ->method('initResultSet')
                          ->will($this->returnValue($zendResultSet));
        $factoryUtilities->expects($this->once())
                          ->method('setAdapterKey')
                          ->will($this->returnSelf());

        $gatewayFactoryMock = $this->GatewayFactory->setMethods(null)->getMock();
        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactoryUtilities', $factoryUtilities);

        $gatewayFactoryMock->setServiceLocator($serviceManager);
        $gatewayFactoryMock->setWorker($workerStub);
        $gatewayFactoryMock->run();
    }

    /**
     * @expectedException FdlGatewayManager\Exception\ClassNotExistException
     * @group RunException
     */
    public function testRunWithNoWorker()
    {
        $gatewayFactoryMock = $this->GatewayFactory
                                   ->setMethods(null)
                                   ->getMock();

        $factoryUtilities = $this->getMockBuilder('FdlGatewayManager\GatewayFactoryUtilities')
                                  ->setMethods(null)
                                  ->getMock();

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactoryUtilities', $factoryUtilities);

        $gatewayFactoryMock->setServiceLocator($serviceManager);
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
     * @group TableGatewayProxy
     */
    public function testGetTableGatewayProxy()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();
        $tableGateway->setTableGatewayProxy('xzzTABLEzzx');

        $this->assertEquals('xzzTABLEzzx', $tableGateway->getTableGatewayProxy());
    }

    /**
     * @group TableGatewayProxy
     */
    public function testsetTableGatewayProxy()
    {
        $tableGateway = $this->GatewayFactory->setMethods(null)->getMock();
        $this->assertNotNull('zzTABLEzz', $tableGateway->setTableGatewayProxy('xx'));
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
