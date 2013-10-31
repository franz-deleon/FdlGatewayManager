<?php
namespace FdlGatewayManager\Service;

use FdlGatewayManager\Exception;
use FdlGatewayManager\Gateway;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\Db\TableGateway\TableGateway;

class FdlGatewayPluginManager extends AbstractPluginManager
{
    /**
     * Whether or not to share by default; default to false
     *
     * @var bool
     */
    protected $shareByDefault = true;

    /**
     * @var array
     */
    protected $canonicalizedTableGateways = array();

    /**
     * Custom get() override implementation
     *
     * This get will look for "table_gateways" key in "fdl_gateway_plugin_config"
     * and create a TableGateway out of it.
     *
     * <code>
     * 'fdl_gateway_plugin_config' => array(
     *     'table_gateways' => array(
     *         'books' => array(
     *             'entity_name' => 'Events',
                   'result_set_name' => 'hydrating_result_set',
     *         ),
     *     ),
     * )
     * </code>
     *
     * @override
     * @param  string $name
     * @param  array $options
     * @param  bool $usePeeringServiceManagers
     * @return object
     */
    public function get($name, $options = array(), $usePeeringServiceManagers = true)
    {
        $config = $this->getServiceLocator()->get('Config');
        $gatewayConfigKey = $config['fdl_service_listener_options']['config_key'];

        if ($this->has($name)) {
            return parent::get($name, $options, $usePeeringServiceManagers);
        } elseif (!empty($config[$gatewayConfigKey]['table_gateways'])) {
            $name = $this->canonicalizeName($name);

            // canonicalize the array table_gateways first
            $this->canonicalizedGatewayKeys($config[$gatewayConfigKey]['table_gateways']);

            $gatewayParams = $this->getTableGatewayParams($name);
            if (is_string($gatewayParams)) {
                $gatewayParams = array($gatewayParams);
            }

            $gateway = $this->factory($gatewayParams);
            $this->setService($name, $gateway);

            return $gateway;
        }

        return parent::get($name, $options, $usePeeringServiceManagers);
    }

    /**
     * Proxy to factory method
     * @param array $config
     * @return TableGateway
     */
    public function factory(array $config)
    {
        return $this->getServiceLocator()
                    ->get('FdlGatewayManager')
                    ->factory($config);
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

    /**
     * Retrieve the param from the canonicalized gateways
     * @param string $gateway
     * @return multitype:
     */
    public function getTableGatewayParams($gateway)
    {
        if (isset($this->canonicalizedTableGateways[$gateway])) {
            return $this->canonicalizedTableGateways[$gateway];
        }
    }

    /**
     * Canonicalize the gateway keys
     * @param array $tableGateways
     * @return null
     */
    protected function canonicalizedGatewayKeys(array $tableGateways)
    {
        if (empty($this->canonicalizedTableGateways)) {
            foreach ($tableGateways as $key => $val) {
                $this->canonicalizedTableGateways[$this->canonicalizeName($key)] = $val;
            }
        }
    }
}
