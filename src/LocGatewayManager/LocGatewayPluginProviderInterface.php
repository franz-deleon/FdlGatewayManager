<?php

namespace LocGatewayManager;

interface LocGatewayPluginProviderInterface
{
    /**
     * @return array|\Zend\ServiceManager\Config
     */
    public function getLocGatewayPluginConfig();
}
