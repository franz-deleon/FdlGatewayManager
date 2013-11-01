<?php
namespace FdlGatewayManager;

class AdapterManager extends AbstractServiceLocatorAware
{
    /**
     * Collection of adapters
     * @var array
     */
    protected $adapters = array();

    /**
     * Retrieve the adapter
     * If no instance create it
     *
     * @param unknown $name
     * @return \Zend\Db\Adapter\AdapterInterface
     */
    public function get($name)
    {
        if ($this->has($name)) {
            return $this->adapters[$name];
        }

        $serviceManager = $this->getServiceLocator();
        $config  = $serviceManager->get('config');

        // the actual building of the adapter is delegated to the Abstract Factory
        $adapter = $serviceManager->get($config['fdl_gateway_manager_config']['table_gateway']['adapter']);

        // store the adapter
        $this->adapters[$name] = $adapter;

        return $adapter;
    }

    /**
     * Does the adapter with the given name exist?
     * @param string $name
     * @return boolean
     */
    public function has($name)
    {
        if (isset($this->adapters[$name])) {
            return true;
        }
        return false;
    }
}
