<?php
namespace LocGatewayManager;

interface LocGatewayManagerAwareInterface
{
    /**
     * @param Manager $manager
     */
    public function setLocGatewayManager(Manager $manager);
}
