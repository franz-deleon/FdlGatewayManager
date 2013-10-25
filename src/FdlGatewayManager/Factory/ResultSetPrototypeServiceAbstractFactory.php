<?php
namespace FdlGatewayManager\Factory;

use FdlGatewayManager\Exception;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\ServiceManager;

class ResultSetPrototypeServiceAbstractFactory implements ServiceManager\AbstractFactoryInterface
{
    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config = $serviceLocator->get('config');
        $serviceName = $config['fdl_gateway_manager_config']['table_gateway']['result_set_prototype'];

        return ($requestedName === $serviceName);
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceManager\ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $config  = $serviceLocator->get('config');
        $resultSetPrototypeName = $serviceLocator->get('FdlGatewayFactoryEvent')->getResultSetPrototypeName();

        if (isset($resultSetPrototypeName)) {
            if (class_exists($resultSetPrototypeName)) {
                $resultSetPrototypeClass = $resultSetPrototypeName;
            } else {
                $wordfilter = new UnderscoreToCamelCase();
                $resultSetPrototypeName = $wordfilter->filter($resultSetPrototypeName);
                $resultSetPrototypeClass =  "FdlGatewayManager\\ResultSet\\{$resultSetPrototypeName}";
                if (!class_exists($resultSetPrototypeClass)) {
                    throw new Exception\ClassNotExistException('Class: ' . $resultSetPrototypeClass . ', does not exist.');
                }
            }

            // initialize
            return new $resultSetPrototypeClass();
        }

        return new \stdClass();
    }
}
