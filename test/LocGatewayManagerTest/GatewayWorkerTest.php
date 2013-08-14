<?php
namespace LocGatewayManagerTest;

use LocGatewayManager;

/**
 * GatewayWorker test case.
 */
class GatewayWorkerTest extends \PHPUnit_Framework_TestCase
{
    /**
	 * @var GatewayWorker
	 */
    private $GatewayWorker;

    /**
	 * Prepares the environment before running a test.
	 */
    protected function setUp()
    {
        parent::setUp();
        $this->GatewayWorker = new LocGatewayManager\GatewayWorker(/* parameters */);
    }

    /**
	 * Cleans up the environment after running a test.
	 */
    protected function tearDown()
    {
        $this->GatewayWorker = null;
        parent::tearDown();
    }

    /**
	 * Tests GatewayWorker->assemble()
	 */
    public function testAssemble()
    {
        $smStub = $this->getMockBuilder('\Zend\ServiceManager\ServiceManager')
                       ->setMethods(array('get'))
                       ->disableOriginalConstructor()
                       ->getMock();
        $smStub->expects($this->once())
               ->method('get')
               ->will($this->returnValue(
                   $this->getMock('\Zend\Db\TableGateway\TableGateway', array(), array(), '', false)
               ));

        $factoryMock = $this->getMockBuilder('LocGatewayManager\GatewayFactory')
                            ->setMethods(array('setTableGateway'))
                            ->disableOriginalConstructor()
                            ->getMock();
        $factoryMock->expects($this->any())
                    ->method('setTableGateway')
                    ->with($this->isInstanceOf('\Zend\Db\TableGateway\TableGateway'))
                    ->will($this->returnSelf());

        $this->GatewayWorker->setServiceLocator($smStub);
        $this->GatewayWorker->assemble($factoryMock);
    }

    /**
	 * Tests GatewayWorker->getAdapterKeyName()
	 * @group AdapterKeyName
	 */
    public function testGetAdapterKeyName()
    {
        $this->assertNull($this->GatewayWorker->getAdapterKeyName());
    }

    /**
	 * Tests GatewayWorker->setAdapterKeyName()
	 * @group AdapterKeyName
	 */
    public function testSetAdapterKeyName()
    {
        $this->GatewayWorker->setAdapterKeyName('xx1234xx');
        $this->assertEquals(
            'xx1234xx',
            $this->GatewayWorker->getAdapterKeyName()
        );
    }

    /**
	 * Tests GatewayWorker->getEntityName()
	 * @group EntityName
	 */
    public function testGetEntityName()
    {
        $this->assertNull($this->GatewayWorker->getEntityName());
    }

    /**
	 * Tests GatewayWorker->setEntityName()
	 * @group EntityName
	 */
    public function testSetEntityName()
    {
        $this->GatewayWorker->setEntityName('ss1234ss');
        $this->assertEquals(
            'ss1234ss',
            $this->GatewayWorker->getEntityName()
        );
    }

    /**
	 * Tests GatewayWorker->getFeatureName()
	 * @group  FeatureName
	 */
    public function testGetFeatureName()
    {
        $this->assertNull($this->GatewayWorker->getFeatureName());
    }

    /**
	 * Tests GatewayWorker->setFeatureName()
	 * @group FeatureName
	 */
    public function testSetFeatureName()
    {
        $this->GatewayWorker->setFeatureName('ddssdd');
        $this->assertEquals(
            'ddssdd',
            $this->GatewayWorker->getFeatureName()
        );
    }

    /**
	 * Tests GatewayWorker->getResultSetName()
	 * @group ResultSetName
	 */
    public function testGetResultSetName()
    {
        $this->assertNull($this->GatewayWorker->getResultSetName());
    }

    /**
	 * Tests GatewayWorker->setResultSetName()
	 * @group ResultSetName
	 */
    public function testSetResultSetName()
    {
        $this->GatewayWorker->setResultSetName('hithere');
        $this->assertEquals(
            'hithere',
            $this->GatewayWorker->getResultSetName()
        );
    }

    /**
	 * Tests GatewayWorker->getTableName()
	 * @group TableName
	 */
    public function testGetTableName()
    {
        $this->assertNull($this->GatewayWorker->getTableName());
    }

    /**
	 * Tests GatewayWorker->setTableName()
	 */
    public function testSetTableName()
    {
        $this->GatewayWorker->setTableName('hhgghh');
        $this->assertEquals(
            'hhgghh',
            $this->GatewayWorker->getTableName()
        );
    }

    /**
     * @group Reset
     */
    public function testReset()
    {
        $this->GatewayWorker->setAdapterKeyName('someXXXKETYzzz');
        $this->assertEquals('someXXXKETYzzz', $this->GatewayWorker->getAdapterKeyName());
        $this->GatewayWorker->reset();
        $this->assertNull($this->GatewayWorker->getAdapterKeyName());
    }
}

