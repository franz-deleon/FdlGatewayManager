<?php
namespace FdlGatewayManager;

use Zend\EventManager;

abstract class AbstractGatewayFactory extends AbstractServiceLocatorAware implements EventManager\EventManagerAwareInterface
{
    /**
     * @var Zend\EventManager\EventManager
     */
    protected $eventManager;

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
        $this->eventManager = $eventManager->setIdentifiers(array(
            get_class(static::$this),
        ));
        return $this;
    }
}
