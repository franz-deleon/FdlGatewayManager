<?php
namespace FdlGatewayManager\Service;

interface FdlGatewayPluginProviderInterface
{
    /**
     * @return array|\Zend\ServiceManager\Config
     */
    public function getFdlGatewayPluginConfig();
}
