<?php
/**
 *
 * ibmi-tools (https://github.com/fabiopellati/ibmi-tools)
 *
 * @link      https://github.com/fabiopellati/ibmi-tools
 * @copyright Copyright (c) 2017-2017 Fabio Pellati (https://github.com/fabiopellati)
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace IbmiTools\PgmCall\Listener;

use MessageExchangeEventManager\Actuator\Actuator;
use MessageExchangeEventManager\Event\Event;
use MessageExchangeEventManager\EventManagerAwareTrait;
use Psr\Container\ContainerInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class ActuatorListenersAttacherListener
    extends AbstractListenerAggregate
{

    use EventManagerAwareTrait;

    /**
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * ActuatorListenersAttacherListener constructor.
     *
     * @param \Psr\Container\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Attach one or more listeners
     *
     * Implementors may add an optional $priority argument; the EventManager
     * implementation will pass this to the aggregate.
     *
     * @param EventManagerInterface $events
     * @param int                   $priority
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 2500)
    {
        $this->setEventManager($events);
        $this->listeners[] = $events->attach(Actuator::EVENT_ACTUATOR_RUN_PRE, [$this, 'onEvent'], $priority);
    }

    /**
     *
     * @param \MessageExchangeEventManager\Event\Event|\Zend\EventManager\Event $e
     *
     * @return \MessageExchangeEventManager\Response\ResponseInterface
     */
    public function onEvent(Event $e)
    {
        $request = $e->getRequest();
        $response = $e->getResponse();
        try {
            $pgmCallSpec = $request->getParameters()->get('pgmCallSpec');
            $requestedName = $request->getParameters()->get('requestedName');
            if (empty($pgmCallSpec)) {
                throw new \Exception($requestedName . ' config not found.');
            }
            $listeners = $pgmCallSpec['listeners'];
            /**
             * load listeners
             */
            foreach ($listeners as $listenerClass) {
                $listener = $this->getContainer()->get($listenerClass);
                if (method_exists($listener, 'setEventManager')) {
                    $listener->setEventManager($this->getEventManager());
                }
                $listener->attach($this->getEventManager());

            }

        } catch (\Exception $error) {
            $response->setContent($error);
            $e->stopPropagation();
        }

        return $response;
    }

}
