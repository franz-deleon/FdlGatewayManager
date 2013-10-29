<?php
namespace FdlGatewayManager;

use Zend\EventManager\Event;

class GatewayFactoryEvent extends Event
{
    /**
     * Gateway factory events
     */
    const PRE_RUN  = 'pre.run';
}
