<?php
namespace FdlGatewayManager;

use Zend\EventManager\Event;

class GatewayFactoryEvent extends Event
{
    /**
     * Gateway factory events
     */
    const ON_MANAGER_STARTUP  = 'onManagerStartup.FactoryEvent';
    const PRE_RUN             = 'preRun.FactoryEvent';
}
