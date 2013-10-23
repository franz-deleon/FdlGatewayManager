<?php
namespace FdlGatewayManager;

interface FdlGatewayPluginAwareInterface
{
    /**
     * @returns FdlGatewayManager\FdlGatewayManagerPluginManager
     */
    public function getGatewayPlugin();

    /**
     * @param Manager $manager
     */
    public function setGatewayPlugin(FdlGatewayPluginManager $manager);
}
