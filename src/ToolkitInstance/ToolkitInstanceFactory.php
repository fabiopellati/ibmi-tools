<?php
/**
 *
 * ibmi-tools (https://github.com/fabiopellati/ibmi-tools)
 *
 * @link      https://github.com/fabiopellati/ibmi-tools
 * @copyright Copyright (c) 2017-2017 Fabio Pellati (https://github.com/fabiopellati)
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace IbmiTools\ToolkitInstance;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;

class ToolkitInstanceFactory
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
        try {
            $instance = $this->getToolkit($requestedName, $config);

            return $instance;
        } catch (\Exception $e) {
            switch ($e->getCode()) {
                case 8001;
                    $code = 401;
                    break;
                default:
                    $code = 500;
                    break;
            }
            throw new ServiceNotCreatedException($e->getMessage(), $code);

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
            !isset($config['ibmi-tools']['toolkit-instance'][$requestedName]) ||
            !isset($config['ibmi-tools']['toolkit-instance'][$requestedName]['database']) ||
            !isset($config['ibmi-tools']['toolkit-instance'][$requestedName]['user']) ||
            !isset($config['ibmi-tools']['toolkit-instance'][$requestedName]['password'])
        ) {
            throw new ServiceNotCreatedException(vsprintf('%s configuration missed', $requestedName));
        }
        $config_instance = $config['ibmi-tools']['toolkit-instance'][$requestedName];
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