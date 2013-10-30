<?php
namespace FdlGatewayManagerTest;

use FdlGatewayManager\Factory\ResultSetPrototypeServiceAbstractFactory;
/**
 * ResultSetPrototypeServiceAbstractFactory test case.
 */
class ResultSetPrototypeServiceAbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
	 * @var ResultSetPrototypeServiceAbstractFactory
	 */
    private $ResultSetPrototypeServiceAbstractFactory;
    /**
	 * Prepares the environment before running a test.
	 */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated ResultSetPrototypeServiceAbstractFactoryTest::setUp()
        $this->ResultSetPrototypeServiceAbstractFactory = new ResultSetPrototypeServiceAbstractFactory(/* parameters */);
    }
    /**
	 * Cleans up the environment after running a test.
	 */
    protected function tearDown()
    {
        // TODO Auto-generated ResultSetPrototypeServiceAbstractFactoryTest::tearDown()
        $this->ResultSetPrototypeServiceAbstractFactory = null;
        parent::tearDown();
    }
    /**
	 * Constructs the test case.
	 */
    public function __construct()
    {
        // TODO Auto-generated constructor
    }

    /**
	 * Tests ResultSetPrototypeServiceAbstractFactory->canCreateServiceWithName()
	 * @group CanCreateServiceWithName1
	 */
    public function testCanCreateServiceWithName()
    {
        $config['fdl_gateway_manager_config']['table_gateway']['result_set_prototype'] = 'HydratingResultSet';

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $config);

        $result = $this->ResultSetPrototypeServiceAbstractFactory->canCreateServiceWithName($serviceManager, '', 'HydratingResultSet');
        $this->assertTrue($result);
    }

    /**
	 * Tests ResultSetPrototypeServiceAbstractFactory->canCreateServiceWithName()
	 * @group CanCreateServiceWithName2
	 */
    public function testCanCreateServiceWithNameReturnsFalse()
    {
        $config['fdl_gateway_manager_config']['table_gateway']['result_set_prototype'] = 'Different';

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $config);

        $result = $this->ResultSetPrototypeServiceAbstractFactory->canCreateServiceWithName($serviceManager, '', 'HydratingResultSet');
        $this->assertFalse($result);
    }

    /**
	 * Tests ResultSetPrototypeServiceAbstractFactory->createServiceWithName()
	 * @group CreateServiceWithName1
	 */
    public function testCreateServiceWithNameThatPrototypeReturnsDefault()
    {
        // worker event stub
        $gatewayFactoryWorkerEventStub = $this->getMockBuilder('\FdlGatewayManager\GatewayWorkerEvent')->setMethods(array('getResultSetPrototypeName'))->getMock();
        $gatewayFactoryWorkerEventStub->expects($this->once())
            ->method('getResultSetPrototypeName')
            ->will($this->returnValue('default'));

        // tablegateway stub
        $objectPrototypeStub = $this->getMockBuilder('ObjectPrototype')->getMock();

        // factory stub
        $gatewayFactoryStub = $this->getMockBuilder('\FdlGatewayManager\GatewayFactory')->setMethods(array('getWorkerEvent', 'getEntity'))->getMock();
        $gatewayFactoryStub->expects($this->once())
            ->method('getWorkerEvent')
            ->will($this->returnValue($gatewayFactoryWorkerEventStub));
        $gatewayFactoryStub->expects($this->once())
            ->method('getEntity')
            ->will($this->returnValue($objectPrototypeStub));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactory', $gatewayFactoryStub);

        $result = $this->ResultSetPrototypeServiceAbstractFactory->createServiceWithName($serviceManager, '', '');
        $this->assertInstanceOf('Zend\Db\ResultSet\HydratingResultSet', $result);
    }

    /**
	 * Tests ResultSetPrototypeServiceAbstractFactory->createServiceWithName()
	 * @group CreateServiceWithName2
	 */
    public function testCreateServiceWithNameReturnsStdClass()
    {
        // worker event stub
        $gatewayFactoryWorkerEventStub = $this->getMockBuilder('\FdlGatewayManager\GatewayWorkerEvent')->setMethods(array('getResultSetPrototypeName'))->getMock();
        $gatewayFactoryWorkerEventStub->expects($this->once())
            ->method('getResultSetPrototypeName')
            ->will($this->returnValue('CustomPrototype'));

        // factory stub
        $gatewayFactoryStub = $this->getMockBuilder('\FdlGatewayManager\GatewayFactory')->setMethods(array('getWorkerEvent'))->getMock();
        $gatewayFactoryStub->expects($this->once())
            ->method('getWorkerEvent')
            ->will($this->returnValue($gatewayFactoryWorkerEventStub));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactory', $gatewayFactoryStub);

        $result = $this->ResultSetPrototypeServiceAbstractFactory->createServiceWithName($serviceManager, '', '');
        $this->assertInstanceOf('stdClass', $result);
    }
}
