<?php
namespace FdlGatewayManager;

use Zend\ServiceManager;
use Zend\EventManager;

abstract class AbstractServiceLocatorAware implements ServiceManager\ServiceLocatorAwareInterface, EventManager\EventManagerAwareInterface
{
    /**
     * @var Zend\EventManager\EventManager
     */
    protected $eventManager;

    /**
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $serviceLocator;

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

    /**
     * (non-PHPdoc)
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Inject an EventManager instance
     *
     * @param  EventManagerInterface $eventManager
     * @return void
     */
    public function setEventManager(EventManager\EventManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;
        return $this;
    }
}
