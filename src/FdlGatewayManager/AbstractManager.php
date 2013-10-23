<?php
namespace FdlGatewayManager;

use Zend\ServiceManager;

abstract class AbstractManager implements ServiceManager\ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    /**
     * @return GatewayWorker
     */
    public function getGatewayWorker()
    {
        return $this->getServiceLocator()->get('FdlGatewayWorker');
    }

    /**
     * @return GatewayFactory
     */
    public function getGatewayFactory()
    {
        return $this->getServiceLocator()->get('FdlGatewayFactory');
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
