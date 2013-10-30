<?php
namespace FdlGatewayManagerTest;

use FdlGatewayManager\Utils\AdapterServiceUtilities;

/**
 * AdapterServiceUtilities test case.
 */
class AdapterServiceUtilitiesTest extends \PHPUnit_Framework_TestCase
{
    /**
	 * @var AdapterServiceUtilities
	 */
    private $AdapterServiceUtilities;
    /**
	 * Prepares the environment before running a test.
	 */
    protected function setUp()
    {
        parent::setUp();
        // TODO Auto-generated AdapterServiceUtilitiesTest::setUp()
        $this->AdapterServiceUtilities = new AdapterServiceUtilities(/* parameters */);
    }
    /**
	 * Cleans up the environment after running a test.
	 */
    protected function tearDown()
    {
        // TODO Auto-generated AdapterServiceUtilitiesTest::tearDown()
        $this->AdapterServiceUtilities = null;
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
	 * Tests AdapterServiceUtilities->getAdapterKey()
	 * @group GetAdapterKey1
	 */
    public function testGetAdapterKey()
    {
        // worker event stub
        $gatewayFactoryWorkerEventStub = $this->getMockBuilder('\FdlGatewayManager\GatewayWorkerEvent')->setMethods(array('getAdapterKey'))->getMock();
        $gatewayFactoryWorkerEventStub->expects($this->once())
            ->method('getAdapterKey')
            ->will($this->returnValue('sql_server'));

        // factory stub
        $gatewayFactoryStub = $this->getMockBuilder('\FdlGatewayManager\GatewayFactory')->setMethods(array('getWorkerEvent'))->getMock();
        $gatewayFactoryStub->expects($this->once())
            ->method('getWorkerEvent')
            ->will($this->returnValue($gatewayFactoryWorkerEventStub));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactory', $gatewayFactoryStub);

        $this->AdapterServiceUtilities->setServiceLocator($serviceManager);

        $adapterKey = $this->AdapterServiceUtilities->getAdapterKey(/* parameters */);
        $this->assertEquals('sql_server', $adapterKey);
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapterKey()
	 * @group GetAdapterKey2
	 */
    public function testGetAdapterKeyRetursnNull()
    {
        // factory stub
        $gatewayFactoryStub = $this->getMockBuilder('\FdlGatewayManager\GatewayFactory')->setMethods(array('getWorkerEvent'))->getMock();
        $gatewayFactoryStub->expects($this->once())
            ->method('getWorkerEvent')
            ->will($this->returnValue(null));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('FdlGatewayFactory', $gatewayFactoryStub);

        $this->AdapterServiceUtilities->setServiceLocator($serviceManager);

        $adapterKey = $this->AdapterServiceUtilities->getAdapterKey(/* parameters */);
        $this->assertNull($adapterKey);
    }

    /**
	 * Tests AdapterServiceUtilities->getDriverParams()
	 * @group GetDriverParams1
	 */
    public function testGetDriverParams()
    {
        $configStub = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
            'adapter'  => 'GlobalCal/SqlAdapter',
            'options'  => array('profiler'),
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getAdapterConfig'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->once())
            ->method('getAdapterConfig')
            ->will($this->returnValue($configStub));

        $params = $adapterServiceUtilitiesMock->getDriverParams();
        $this->assertArrayNotHasKey('options', $params);
        $this->assertEquals('odbc:Driver=FreeTDS; Server=localhost; Port=2434;', $params['dsn']);
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapter()
	 * @group GetOptions1
	 */
    public function testGetOptions()
    {
        $options = array(
            'buffer_results' => true,
            'adapter_class'  => 'GlobalCal/DefaultAdapter',
            'platform' => 'Oracle',
        );
        $configStub = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
            'adapter'  => 'GlobalCal/SqlAdapter',
            'options' => $options,
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getAdapterConfig'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->once())
            ->method('getAdapterConfig')
            ->will($this->returnValue($configStub));

        $this->assertEquals($options, $adapterServiceUtilitiesMock->getOptions());
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapter()
	 * @group GetOptions2
	 */
    public function testGetOptionsReturnsNull()
    {
        $configStub = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
            'adapter_class'  => 'GlobalCal/SqlAdapter',
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getAdapterConfig'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->once())
            ->method('getAdapterConfig')
            ->will($this->returnValue($configStub));

        $this->assertNull($adapterServiceUtilitiesMock->getOptions());
    }

    /**
	 * Tests AdapterServiceUtilities->getOptionsAdapterClass()
	 * @group GetOptionsAdapterClass1
	 */
    public function testGetOptionsAdapterClass()
    {
        $optionsStub = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
            'adapter_class'  => 'CustomAdapter',
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getOptions'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($optionsStub));

        $customAdapterStub = $this->getMockBuilder('CustomAdapter')->getMock();

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('CustomAdapter', $customAdapterStub);

        $adapterServiceUtilitiesMock->setServiceLocator($serviceManager);
        $this->assertInstanceOf('CustomAdapter', $adapterServiceUtilitiesMock->getOptionsAdapterClass());
    }

    /**
	 * Tests AdapterServiceUtilities->getOptionsAdapterClass()
	 * @group GetOptionsAdapterClass2
	 */
    public function testGetOptionsAdapterClassWhereAdapterClassIsCallable()
    {
        $optionsStub = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
            'adapter_class'  => function () {
                return 1234;
            },
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getOptions'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($optionsStub));

        $serviceManager = Bootstrap::getServiceManager();

        $adapterServiceUtilitiesMock->setServiceLocator($serviceManager);
        $this->assertEquals(1234, $adapterServiceUtilitiesMock->getOptionsAdapterClass());
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapter()
	 * @group GetOptionsAdapterClass3
	 */
    public function testGetOptionsAdapterClassWhereAdapterClassIsObject()
    {
        $optionsStub = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
            'adapter_class'  => new \stdClass(),
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getOptions'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($optionsStub));

        $serviceManager = Bootstrap::getServiceManager();

        $adapterServiceUtilitiesMock->setServiceLocator($serviceManager);
        $this->assertInstanceOf('stdClass', $adapterServiceUtilitiesMock->getOptionsAdapterClass());
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapter()
	 * @group GetAdapter4
	 */
    public function testGetAdapterReturnsNull()
    {
        $optionsStub = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getOptions'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->any())
            ->method('getOptions')
            ->will($this->returnValue($optionsStub));

        $this->assertNull($adapterServiceUtilitiesMock->getOptionsAdapterClass());
    }

    /**
	 * Tests AdapterServiceUtilities->getPlatform()
	 */
    public function testGetPlatform()
    {
        // TODO Auto-generated AdapterServiceUtilitiesTest->testGetPlatform()
        $this->markTestIncomplete("getPlatform test not implemented");
        $this->AdapterServiceUtilities->getPlatform(/* parameters */);
    }
    /**
	 * Tests AdapterServiceUtilities->getQueryResultPrototype()
	 */
    public function testGetQueryResultPrototype()
    {
        // TODO Auto-generated AdapterServiceUtilitiesTest->testGetQueryResultPrototype()
        $this->markTestIncomplete("getQueryResultPrototype test not implemented");
        $this->AdapterServiceUtilities->getQueryResultPrototype(/* parameters */);
    }

    /**
	 * Tests AdapterServiceUtilities->getProfiler()
	 */
    public function testGetProfiler()
    {
        // TODO Auto-generated AdapterServiceUtilitiesTest->testGetProfiler()
        $this->markTestIncomplete("getProfiler test not implemented");
        $this->AdapterServiceUtilities->getProfiler(/* parameters */);
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapterConfig()
	 * @group GetAdapterConfig1
	 */
    public function testGetAdapterConfig()
    {
        $sqlDeveloper = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
        );
        $dbConfig = array(
            'fdl_db' => array(
                'sql_developer' => $sqlDeveloper,
            ),
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getAdapterKey'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->once())
            ->method('getAdapterKey')
            ->will($this->returnValue('sql_developer'));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilitiesMock->setServiceLocator($serviceManager);

        $config = $adapterServiceUtilitiesMock->getAdapterConfig(/* parameters */);
        $this->assertEquals($sqlDeveloper, $config);
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapterConfig()
	 * @group GetAdapterConfig2
	 */
    public function testGetAdapterConfigUsesDefaultAsKey()
    {
        $sqlDeveloper = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
        );
        $dbConfig = array(
            'fdl_db' => array(
                'default' => $sqlDeveloper,
            ),
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getAdapterKey'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->once())
            ->method('getAdapterKey')
            ->will($this->returnValue(null));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilitiesMock->setServiceLocator($serviceManager);

        $config = $adapterServiceUtilitiesMock->getAdapterConfig(/* parameters */);
        $this->assertEquals($sqlDeveloper, $config);
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapterConfig()
	 * @group GetAdapterConfig3
	 */
    public function testGetAdapterConfigRetrievesFirstArray()
    {
        $sqlDeveloper = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
        );
        $dbConfig = array(
            'fdl_db' => array(
                $sqlDeveloper,
            ),
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getAdapterKey'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->once())
            ->method('getAdapterKey')
            ->will($this->returnValue(null));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilitiesMock->setServiceLocator($serviceManager);

        $config = $adapterServiceUtilitiesMock->getAdapterConfig(/* parameters */);
        $this->assertEquals($sqlDeveloper, $config);
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapterConfig()
	 * @group GetAdapterConfig4
	 */
    public function testGetAdapterConfigWhereKeyIsArray()
    {
        $sqlDeveloper = array(
            'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
            'username' => 'gcalender',
            'password' => 'pas$w0rd',
        );
        $dbConfig = array(
            'fdl_db' => $sqlDeveloper,
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getAdapterKey'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->once())
            ->method('getAdapterKey')
            ->will($this->returnValue(null));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilitiesMock->setServiceLocator($serviceManager);

        $config = $adapterServiceUtilitiesMock->getAdapterConfig(/* parameters */);
        $this->assertEquals($sqlDeveloper, $config);
    }

    /**
	 * Tests AdapterServiceUtilities->getAdapterConfig()
	 * @expectedException FdlGatewayManager\Exception\ErrorException
	 * @group GetAdapterConfig5
	 */
    public function testGetAdapterConfigReturnsException()
    {
        $dbConfig = array(
            'fdl_db' => '',
        );

        $adapterServiceUtilitiesMock = $this->getMockBuilder('FdlGatewayManager\Utils\AdapterServiceUtilities')->setMethods(array('getAdapterKey'))->getMock();
        $adapterServiceUtilitiesMock->expects($this->once())
            ->method('getAdapterKey')
            ->will($this->returnValue(null));

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilitiesMock->setServiceLocator($serviceManager);

        $adapterServiceUtilitiesMock->getAdapterConfig(/* parameters */);
    }

    /**
	 * Tests AdapterServiceUtilities->getDbConfig()
	 * @group GetDbConfig1
	 */
    public function testGetDbConfigForFdlDb()
    {
        $sqlConfig = array(
            'sql_developer' => array(
                'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
                'username' => 'gcalender',
                'password' => 'pas$w0rd',
                'adapter_class'  => 'CustomAdapter',
            ),
        );
        $dbConfig = array(
            'fdl_db' => $sqlConfig
        );

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilities = $this->AdapterServiceUtilities->setServiceLocator($serviceManager);
        $config = $this->AdapterServiceUtilities->getDbConfig(/* parameters */);

        $this->assertEquals($sqlConfig, $config);
    }

    /**
	 * Tests AdapterServiceUtilities->getDbConfig()
	 * @group GetDbConfig2
	 */
    public function testGetDbConfigForFdlDatabase()
    {
        $sqlConfig = array(
            'sql_developer' => array(
                'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
                'username' => 'gcalender',
                'password' => 'pas$w0rd',
                'adapter_class'  => 'CustomAdapter',
            ),
        );
        $dbConfig = array(
            'fdl_database' => $sqlConfig
        );

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilities = $this->AdapterServiceUtilities->setServiceLocator($serviceManager);
        $config = $this->AdapterServiceUtilities->getDbConfig(/* parameters */);

        $this->assertEquals($sqlConfig, $config);
    }

    /**
	 * Tests AdapterServiceUtilities->getDbConfig()
	 * @group GetDbConfig3
	 */
    public function testGetDbConfigForDb()
    {
        $sqlConfig = array(
            'sql_developer' => array(
                'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
                'username' => 'gcalender',
                'password' => 'pas$w0rd',
                'adapter_class'  => 'CustomAdapter',
            ),
        );
        $dbConfig = array(
            'db' => $sqlConfig
        );

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilities = $this->AdapterServiceUtilities->setServiceLocator($serviceManager);
        $config = $this->AdapterServiceUtilities->getDbConfig(/* parameters */);

        $this->assertEquals($sqlConfig, $config);
    }

    /**
	 * Tests AdapterServiceUtilities->getDbConfig()
	 * @group GetDbConfig4
	 */
    public function testGetDbConfigForDatabase()
    {
        $sqlConfig = array(
            'sql_developer' => array(
                'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
                'username' => 'gcalender',
                'password' => 'pas$w0rd',
                'adapter_class'  => 'CustomAdapter',
            ),
        );
        $dbConfig = array(
            'database' => $sqlConfig
        );

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilities = $this->AdapterServiceUtilities->setServiceLocator($serviceManager);
        $config = $this->AdapterServiceUtilities->getDbConfig(/* parameters */);

        $this->assertEquals($sqlConfig, $config);
    }

    /**
	 * Tests AdapterServiceUtilities->getDbConfig()
	 * @expectedException FdlGatewayManager\Exception\InvalidArgumentException
	 * @group GetDbConfig5
	 */
    public function testGetDbConfigReturnsException()
    {
        $sqlConfig = array(
            'sql_developer' => array(
                'dsn'      => 'odbc:Driver=FreeTDS; Server=localhost; Port=2434;',
                'username' => 'gcalender',
                'password' => 'pas$w0rd',
                'adapter_class'  => 'CustomAdapter',
            ),
        );
        $dbConfig = array(
            'non_db' => $sqlConfig
        );

        $serviceManager = Bootstrap::getServiceManager()->setAllowOverride(true);
        $serviceManager->setService('Config', $dbConfig);

        $adapterServiceUtilities = $this->AdapterServiceUtilities->setServiceLocator($serviceManager);
        $config = $this->AdapterServiceUtilities->getDbConfig(/* parameters */);

        $this->assertEquals($sqlConfig, $config);
    }
}

