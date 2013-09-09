<?php
namespace LocGatewayManager;

interface LocGatewayPluginAwareInterface
{
    /**
     * @returns LocGatewayManager\LocGatewayManagerPluginManager
     */
    public function getLocGatewayPlugin();

    /**
     * @param Manager $manager
     */
    public function setLocGatewayPlugin(LocGatewayPluginManager $manager);
}
