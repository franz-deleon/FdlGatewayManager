<?php
namespace FdlGatewayManager;

interface FdlGatewayPluginProviderInterface
{
    /**
     * @return array|\Zend\ServiceManager\Config
     */
    public function getFdlGatewayPluginConfig();
}
