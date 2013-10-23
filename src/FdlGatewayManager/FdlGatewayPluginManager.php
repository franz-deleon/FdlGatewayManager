<?php
namespace FdlGatewayManager;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\Db\TableGateway\TableGateway;

class FdlGatewayPluginManager extends AbstractPluginManager
{
    /**
     * Whether or not to share by default; default to false
     *
     * @var bool
     */
    protected $shareByDefault = false;

    /**
     * Proxy to factory method
     * @param array $config
     * @return TableGateway
     */
    public function factory(array $config)
    {
        return $this->getFdlGatewayManager()->factory($config);
    }

    /**
     * Proxy to gateway manager
     * @return Manager
     */
    public function getFdlGatewayManager()
    {
        return $this->getServiceLocator()->get('FdlGatewayManager');
    }

    /**
     * (non-PHPdoc)
     * @see \Zend\ServiceManager\AbstractPluginManager::validatePlugin()
     */
    public function validatePlugin($plugin)
    {
        if (!$plugin instanceof TableGateway && !$plugin instanceof Gateway\AbstractTable) {
            throw new Exception\ErrorException(sprintf(
                'Plugin of type %s is invalid; must implement TableGateway or %s\Gateway\AbstractTable',
                (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
                __NAMESPACE__
            ));
        }
    }
}
