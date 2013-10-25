<?php
namespace FdlGatewayManager\Factory;

use Zend\ServiceManager;

abstract class AbstractServiceLocatorAware implements ServiceManager\ServiceLocatorAwareInterface
{
    protected $serviceLocator;

    public function setServiceLocator(ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }

    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
}
