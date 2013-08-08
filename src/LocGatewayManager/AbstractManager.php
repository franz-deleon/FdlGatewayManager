<?php
namespace LocGatewayManager;

use Zend\ServiceManager;

abstract class AbstractManager implements ServiceManager\ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    /**
     * @return GatewayWorker
     */
    public function getGatewayWorker()
    {
        return $this->getServiceLocator()->get('LocGatewayWorker');
    }

    /**
     * @return GatewayFactory
     */
    public function getGatewayFactory()
    {
        return $this->getServiceLocator()->get('LocGatewayFactory');
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
