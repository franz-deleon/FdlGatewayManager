<?php
namespace FdlGatewayManager\Factory;

use FdlGatewayManager\Exception;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\ServiceManager;

class FeaturesServiceAbstractFactory implements ServiceManager\AbstractFactoryInterface
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
        $serviceName = $config['fdl_gateway_manager_config']['table_gateway']['features'];

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
        $featureName = $serviceLocator->get('FdlGatewayFactory')->getWorkerEvent()->getFeatureName();

        if (isset($featureName)) {
            if (class_exists($featureName)) {
                $featureClass = $featureName;
            } else {
                $wordfilter = new UnderscoreToCamelCase();
                $featureName = $wordfilter->filter($featureName);
                $featureClass = __NAMESPACE__ . "\\Feature\\{$featureName}";
                if (!class_exists($featureClass)) {
                    throw new Exception\ClassNotExistException('Class: ' . $featureClass . ', does not exist.');
                }
            }

            // initialize feature
            return new $featureClass();
        }

        return new \stdClass();
    }
}
