<?php
namespace FdlGatewayManager\AbstractFactory;

use FdlGatewayManager\Exception;
use Zend\Db\Adapter\AdapterInterface;
use Zend\ServiceManager;

class AdapterServiceAbstractFactory implements ServiceManager\AbstractFactoryInterface
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
        $serviceName = $config['fdl_gateway_manager_config']['table_gateway']['adapter'];

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
        $adapter = $config['fdl_gateway_manager_config']['adapter'];
        $adapterPreparer = $serviceLocator->get('FdlAdapterPreparerUtility');
        $workerEvent = $serviceLocator->get('FdlGatewayFactory')->getWorkerEvent();

        // destroy any existing seed
        $adapterPreparer->destroySeed();

        // seeds the preparer
        if (isset($workerEvent)) {
            $adapterPreparer->seed($workerEvent->getAdapterKey());
        }

        try {
            // we need to use a try catch block because "Zend\Db\Adapter\Adapter" will result
            // a "true" when used against an $sm->has()
            $adapter = $adapterPreparer->getAdapterClass() ?: $serviceLocator->get($adapter);
        } catch (\Exception $e) {
            if (!class_exists($adapter)) {
                throw new Exception\ErrorException('Adapter class: "' . $adapter . '" does not exist');
            }

            $adapter = new $adapter(
                $adapterPreparer->getOptionDriverParams(),
                $adapterPreparer->getOptionPlatform(),
                $adapterPreparer->getOptionQueryResultPrototype(),
                $adapterPreparer->getOptionProfiler()
            );
        }

        if (!$adapter instanceof AdapterInterface) {
            throw new Exception\ErrorException('Adapter class: "' . $adapter . '" is not of Zend\Db\Adapter\AdapterInterface');
        }

        return $adapter;
    }
}
