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

use MessageExchangeEventManager\Actuator\ActuatorRunAwareInterface;
use MessageExchangeEventManager\Event\Event;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class SetToolkitInstanceDefaultListener
    extends AbstractListenerAggregate
{

    /**
     * @var \ToolkitApi\Toolkit
     */
    protected $toolkitInstance;

    public function __construct($toolkitInstance)
    {
        $this->toolkitInstance = $toolkitInstance;
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
    public function attach(EventManagerInterface $events, $priority = 5000)
    {

        $this->listeners[] = $events->attach(
            ActuatorRunAwareInterface::EVENT_ACTUATOR_RUN_PRE,
            [$this, 'onEvent'],
            $priority);
    }

    /**
     *
     * @param \MessageExchangeEventManager\Event\Event $e
     *
     * @return \MessageExchangeEventManager\Response\ResponseInterface
     */
    public function onEvent(Event $e)
    {
        $request = $e->getRequest();
        $response = $e->getResponse();
        try {
            $request->getParameters()->set('toolkitInstance', $this->toolkitInstance);
        } catch (\Exception $error) {
            $response->setContent($error);
            $e->stopPropagation();
        }

        return $response;
    }

}
