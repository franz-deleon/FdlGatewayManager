<?php
namespace FdlGatewayManager\Factory;

use FdlGatewayManager\Exception;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\ServiceManager;

class SqlServiceAbstractFactory implements ServiceManager\AbstractFactoryInterface
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
        $serviceName = $config['fdl_gateway_manager_config']['table_gateway']['sql'];

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
        $sqlName = $serviceLocator->get('FdlGatewayFactory')->getWorkerEvent()->getSqlName();

        if (isset($sqlName)) {
            if (class_exists($sqlName)) {
                $sqlClass = $sqlName;
            } else {
                $wordfilter = new UnderscoreToCamelCase();
                $sqlName = $wordfilter->filter($sqlName);
                $sqlClass = __NAMESPACE__ . "\\Sql\\{$sqlName}";
                if (!class_exists($sqlClass)) {
                    throw new Exception\ClassNotExistException('Class: ' . $sqlClass . ', does not exist.');
                }
            }

            // initialize sql
            return new $sqlClass();
        }

        return new \stdClass();
    }
}
