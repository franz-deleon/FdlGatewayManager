<?php
namespace LocGatewayManager;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\Db\TableGateway\TableGateway;

class LocGatewayPluginManager extends AbstractPluginManager
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
     * @param string $index
     * @return TableGateway
     */
    public function factory(array $config, $index)
    {
        return $this->getLocGatewayManager()->factory($config, $index);
    }

    /**
     * Proxy to gateway manager
     * @return Manager
     */
    public function getLocGatewayManager()
    {
        return $this->getServiceLocator()->get('LocGatewayManager');
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
