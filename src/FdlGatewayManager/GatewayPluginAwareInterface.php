<?php
namespace FdlGatewayManager;

interface GatewayPluginAwareInterface
{
    /**
     * @returns FdlGatewayManager\Service\FdlGatewayManagerPluginManager
     */
    public function getGatewayPlugin();

    /**
     * @param Manager $manager
     */
    public function setGatewayPlugin(Service\FdlGatewayPluginManager $manager);
}
