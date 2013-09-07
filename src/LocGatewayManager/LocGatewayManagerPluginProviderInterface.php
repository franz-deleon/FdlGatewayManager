<?php

namespace LocGatewayManager;

interface LocGatewayManagerPluginProviderInterface
{
    /**
     * @return array|\Zend\ServiceManager\Config
     */
    public function getLocGatewayPluginConfig();
}
