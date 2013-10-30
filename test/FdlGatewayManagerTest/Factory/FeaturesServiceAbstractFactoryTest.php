<?php
namespace FdlGatewayManagerTest;

use FdlGatewayManager\Factory\FeaturesServiceAbstractFactory;

/**
 * FeaturesServiceAbstractFactory test case.
 */
class FeaturesServiceAbstractFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
	 * @var FeaturesServiceAbstractFactory
	 */
    private $FeaturesServiceAbstractFactory;
    /**
	 * Prepares the environment before running a test.
	 */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated FeaturesServiceAbstractFactoryTest::setUp()
        $this->FeaturesServiceAbstractFactory = new FeaturesServiceAbstractFactory(/* parameters */);
    }
    /**
	 * Cleans up the environment after running a test.
	 */
    protected function tearDown()
    {
        // TODO Auto-generated FeaturesServiceAbstractFactoryTest::tearDown()
        $this->FeaturesServiceAbstractFactory = null;
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
	 * Tests FeaturesServiceAbstractFactory->canCreateServiceWithName()
	 * @group CanCreateServiceWithName1
	 */
    public function testCanCreateServiceWithName()
    {
        $configStub['fdl_gateway_manager_config']['table_gateway']['features'] = 'FeatureOne';

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $configStub);

        $result = $this->FeaturesServiceAbstractFactory->canCreateServiceWithName($serviceManager, '', 'FeatureOne');
        $this->assertTrue($result);
    }

    /**
	 * Tests FeaturesServiceAbstractFactory->canCreateServiceWithName()
	 * @group CanCreateServiceWithName2
	 */
    public function testCanCreateServiceWithNameReturnsFalse()
    {
        $configStub['fdl_gateway_manager_config']['table_gateway']['features'] = 'FeatureOne';

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $configStub);

        $result = $this->FeaturesServiceAbstractFactory->canCreateServiceWithName($serviceManager, '', 'FeatureTwo');
        $this->assertFalse($result);
    }

    /**
	 * Tests FeaturesServiceAbstractFactory->createServiceWithName()
	 * @group CreateServiceWithName
	 */
    public function testCreateServiceWithWithDefaultFeatureNameAndValidPrimaryKeyProperty()
    {
        // worker event stub
        $gatewayFactoryWorkerEventStub = $this->getMockBuilder('\FdlGatewayManager\GatewayWorkerEvent')->setMethods(array('getFeatureName'))->getMock();
        $gatewayFactoryWorkerEventStub->expects($this->once())
            ->method('getFeatureName')
            ->will($this->returnValue('RowGatewayFeature'));

        // tablegateway stub
        $tableGatewayStub = $this->getMockBuilder('TableGatewayObject')->getMock();
        $tableGatewayStub->primaryKey = 'TABLE';

        // factory stub
        $gatewayFactoryStub = $this->getMockBuilder('\FdlGatewayManager\GatewayFactory')->setMethods(array('getWorkerEvent', 'getTableGateway'))->getMock();
        $gatewayFactoryStub->expects($this->once())
            ->method('getWorkerEvent')
            ->will($this->returnValue($gatewayFactoryWorkerEventStub));
        $gatewayFactoryStub->expects($this->once())
            ->method('getTableGateway')
            ->will($this->returnValue($tableGatewayStub));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactory', $gatewayFactoryStub);

        $rowGatewayFeature = $this->FeaturesServiceAbstractFactory->createServiceWithName($serviceManager, '', '');
        $this->assertInstanceOf('Zend\Db\TableGateway\Feature\RowGatewayFeature', $rowGatewayFeature);
    }

    /**
	 * Tests FeaturesServiceAbstractFactory->createServiceWithName()
	 * @group CreateServiceWithName2
	 */
    public function testCreateServiceWithWithDefaultFeatureNameAsDefaultAndValidPrimaryKeyMethod()
    {
        // worker event stub
        $gatewayFactoryWorkerEventStub = $this->getMockBuilder('\FdlGatewayManager\GatewayWorkerEvent')->setMethods(array('getFeatureName'))->getMock();
        $gatewayFactoryWorkerEventStub->expects($this->once())
            ->method('getFeatureName')
            ->will($this->returnValue('default'));

        // tablegateway stub
        $tableGatewayStub = $this->getMockBuilder('TableGatewayObject')->setMethods(array('getPrimaryKey'))->getMock();
        $tableGatewayStub->expects($this->exactly(2))
            ->method('getPrimaryKey')
            ->will($this->returnValue('TABLE'));

        // factory stub
        $gatewayFactoryStub = $this->getMockBuilder('\FdlGatewayManager\GatewayFactory')->setMethods(array('getWorkerEvent', 'getTableGateway'))->getMock();
        $gatewayFactoryStub->expects($this->once())
            ->method('getWorkerEvent')
            ->will($this->returnValue($gatewayFactoryWorkerEventStub));
        $gatewayFactoryStub->expects($this->once())
            ->method('getTableGateway')
            ->will($this->returnValue($tableGatewayStub));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactory', $gatewayFactoryStub);

        $rowGatewayFeature = $this->FeaturesServiceAbstractFactory->createServiceWithName($serviceManager, '', '');
        $this->assertInstanceOf('Zend\Db\TableGateway\Feature\RowGatewayFeature', $rowGatewayFeature);
    }

    /**
	 * Tests FeaturesServiceAbstractFactory->createServiceWithName()
	 * @group CreateServiceWithName3
	 */
    public function testCreateServiceWithDefaultFeatureNameAndNoPrimaryKey()
    {
        // worker event stub
        $gatewayFactoryWorkerEventStub = $this->getMockBuilder('\FdlGatewayManager\GatewayWorkerEvent')->setMethods(array('getFeatureName'))->getMock();
        $gatewayFactoryWorkerEventStub->expects($this->once())
            ->method('getFeatureName')
            ->will($this->returnValue('default'));

        // tablegateway stub
        $tableGatewayStub = $this->getMockBuilder('TableGatewayObject')->getMock();

        // factory stub
        $gatewayFactoryStub = $this->getMockBuilder('\FdlGatewayManager\GatewayFactory')->setMethods(array('getWorkerEvent', 'getTableGateway'))->getMock();
        $gatewayFactoryStub->expects($this->once())
            ->method('getWorkerEvent')
            ->will($this->returnValue($gatewayFactoryWorkerEventStub));
        $gatewayFactoryStub->expects($this->once())
            ->method('getTableGateway')
            ->will($this->returnValue($tableGatewayStub));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactory', $gatewayFactoryStub);

        $rowGatewayFeature = $this->FeaturesServiceAbstractFactory->createServiceWithName($serviceManager, '', '');
        $this->assertInstanceOf('Zend\Db\TableGateway\Feature\RowGatewayFeature', $rowGatewayFeature);
    }

    /**
	 * Tests FeaturesServiceAbstractFactory->createServiceWithName()
	 * @group CreateServiceWithName4
	 */
    public function testCreateServiceReturnsStdClass()
    {
        // worker event stub
        $gatewayFactoryWorkerEventStub = $this->getMockBuilder('\FdlGatewayManager\GatewayWorkerEvent')->setMethods(array('getFeatureName'))->getMock();
        $gatewayFactoryWorkerEventStub->expects($this->once())
            ->method('getFeatureName')
            ->will($this->returnValue('CustomFeatureName'));

        // factory stub
        $gatewayFactoryStub = $this->getMockBuilder('\FdlGatewayManager\GatewayFactory')->setMethods(array('getWorkerEvent'))->getMock();
        $gatewayFactoryStub->expects($this->once())
            ->method('getWorkerEvent')
            ->will($this->returnValue($gatewayFactoryWorkerEventStub));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactory', $gatewayFactoryStub);

        $rowGatewayFeature = $this->FeaturesServiceAbstractFactory->createServiceWithName($serviceManager, '', '');
        $this->assertInstanceOf('stdClass', $rowGatewayFeature);
    }
}

