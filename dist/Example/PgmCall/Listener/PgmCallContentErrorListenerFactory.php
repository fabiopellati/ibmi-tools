<?php
/**
 *
 * ibmi-tools (https://github.com/fabiopellati/ibmi-tools)
 *
 * @link      https://github.com/fabiopellati/ibmi-tools
 * @copyright Copyright (c) 2017-2017 Fabio Pellati (https://github.com/fabiopellati)
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Example\PgmCall\Listener;
;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

class PgmCallContentErrorListenerFactory
    implements FactoryInterface
{

    /**
     *
     *
     * @param \Interop\Container\ContainerInterface $container
     * @param                                       $requestedName
     * @param array|null                            $options
     *
     * @return object
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        $spec = $config['example'][$requestedName];
        try {
            $instance = $this->getToolkit($requestedName, $config);

            return $instance;
        } catch (\Exception $e) {
            throw new ServiceNotCreatedException($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @param $requestedName
     * @param $config
     *
     * @return \ToolkitApi\Toolkit
     * @internal param $config_instance
     *
     */
    protected function getToolkit($requestedName, $config)
    {
        if (
            !isset($config['ibmi-tools']['toolkit']['instance'][$requestedName]) ||
            !isset($config['ibmi-tools']['toolkit']['instance'][$requestedName]['database']) ||
            !isset($config['ibmi-tools']['toolkit']['instance'][$requestedName]['user']) ||
            !isset($config['ibmi-tools']['toolkit']['instance'][$requestedName]['password'])
        ) {
            throw new ServiceNotCreatedException('Toolkit Configuration\'s  instance missed');
        }
        $config_instance = $config['ibmi-tools']['toolkit']['instance'][$requestedName];
        if (!isset($config_instance['transport'])) {
            $config_instance['transport'] = '';
        }
        if (!isset($config_instance['peristence'])) {
            $config_instance['persistence'] = false;
        }
        $toolkit = \ToolkitService::getInstance($config_instance['database'], $config_instance['user'],
                                                $config_instance['password'], $config_instance['transport'],
                                                $config_instance['persistence']);
        if (isset($config_instance['options']) && is_array($config_instance['options'])) {
            $toolkit->setOptions($config_instance['options']);
        }

        return $toolkit;
    }

}