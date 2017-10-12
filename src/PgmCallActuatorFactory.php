<?php
/**
 *
 * ibmi-tools (https://github.com/fabiopellati/ibmi-tools)
 *
 * @link      https://github.com/fabiopellati/ibmi-tools
 * @copyright Copyright (c) 2017-2017 Fabio Pellati (https://github.com/fabiopellati)
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace IbmiTools;

use IbmiTools\Exception\InvalidParamException;
use IbmiTools\PgmCall\Listener\ActuatorListenersAttacherListener;
use Interop\Container\ContainerInterface;
use MessageExchangeEventManager\Actuator\Actuator;
use MessageExchangeEventManager\Event\Event;
use MessageExchangeEventManager\Request\Request;
use MessageExchangeEventManager\Response\Response;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Stdlib\ArrayUtils;

class PgmCallActuatorFactory
    implements FactoryInterface
{

    /**
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
        $pgmCallSpec = $this->getConfig($requestedName, $config);
        $actuator = new Actuator();
        $actuator->setEvent($this->getEvent());
        $actuator->getEvent()->getRequest()->getParameters()->set('requestedName', $requestedName);
        $actuator->getEvent()->getRequest()->getParameters()->set('pgmCallSpec', $pgmCallSpec);
        $actuatorAttachListenerListener = new ActuatorListenersAttacherListener($container);
        $actuatorAttachListenerListener->attach($actuator->getEventManager());

        return $actuator;

    }

    /**
     *
     * @return \MessageExchangeEventManager\Event\Event
     */
    protected function getEvent()
    {
        $request = new Request();
        $response = new Response();
        $event = new Event();
        $event->setRequest($request);
        $event->setResponse($response);

        return $event;
    }

    /**
     * prepara la configurazione del pgm-call
     * se ci sono parametri di default li compila
     *
     * @param $requestedName
     * @param $config
     *
     * @return array
     * @throws \IbmiTools\Exception\InvalidParamException
     */
    protected function getConfig($requestedName, $config)
    {
        if (
            !isset($config['ibmi-tools']['pgm-call'][$requestedName]) ||
            !isset($config['ibmi-tools']['pgm-call'][$requestedName]['params'])
        ) {
            throw new ServiceNotCreatedException('Configuration: missed for ' . $requestedName);
        }
        $pgmCallDefault = $config['ibmi-tools']['pgm-call-default'];
        $PgmCallSpec = $config['ibmi-tools']['pgm-call'][$requestedName];
        $configDefaultParams = $pgmCallDefault['params'];
        unset($pgmCallDefault['params']);
        foreach ($PgmCallSpec['params'] as $key => $paramSpec) {
            if (is_string($paramSpec)) {
                if (!isset($configDefaultParams[$paramSpec])) {
                    throw new InvalidParamException(vsprintf('default param %s undefined', [$paramSpec]));
                }
                $PgmCallSpec['params'][$key] = $configDefaultParams[$paramSpec];

            } else {
                if (empty($paramSpec['name'])) {
                    if (!isset($configDefaultParams[$key])) {
                        throw new InvalidParamException(vsprintf('default param %s undefined', [$key]));
                    }
                    $PgmCallSpec['params'][$key] = ArrayUtils::merge($configDefaultParams[$key], $paramSpec);
                }
            }
        }
        $mergedConfig = ArrayUtils::merge($pgmCallDefault, $PgmCallSpec);

        return $mergedConfig;
    }

}