GatewayManager
==============

The Gateway Manager is a Zend Framework 2 module designed as a wrapper for Zend\Db\TableGateway\TableGateway
so that a developer can easily assemble and manage multiple gateways with ease without cluttering your
module.php.  

This is useful if you are using mappers, dbtables and entities. The Gateway Manager will automatically inject
and map your entities to your dbtables.

Requirements
------------

*  Zend Framework 2.2
*  PHP 5.3

Usage
-----

1. In your module's module.config.php add:

        // module.config.php inside an array
        'loc_gateway_manager_assets' => array(
            'entities' => 'namespace\to\entity\dir',
            'tables'   => 'namespace\to\tables\dir',
        )

2. Create an entity in your specified 'entities' namespace:

        // Users.php
        namespace namespace\to\entity\dir;
        class Users
        {
            public function getID() ...
            public function setID() ...
            public function getName() ...
            public function setName($name) ...
        }
        
   If you are using a table to create with your mapper

        // UsersTable.php
        namespace namespace\to\tables\dir;
        class UsersTable implements \LocGatewayManager\Gateway\AbstractTable
        {
            $tableName  = 'MY_USERS'; // actual table name in db
            $primaryKey = 'ID' // primary key column
        }
        
   \* This table will act as your inteded tablegateway

3. Use the FactoryManager:

        $gm = $sm->get('LocGatewayManager');
        $tableGateway = $gm->factory(array(
            'entity_name'     => 'Users',
            'result_set_name' => 'HydratingResultSet'
        ), 'users');
        $tableGateway->fetchAll();
        
        // later anywhere where you can have a service manager
        $users = $sm->get('LocGatewayManager')->get('users');
        $users->fetchAll();

##### TODOS:
@todo rename GatewayFactoryProcessor to GatewayFactoryUtilities  
@todo add assemble() to WorkerInterface
