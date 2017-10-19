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

class AddLibrariesListener
    extends AbstractListenerAggregate
{

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
    public function attach(EventManagerInterface $events, $priority = 1000)
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
            $toolkitInstance = $request->getParameters()->get('toolkitInstance');
            $pgmCallSpec = $request->getParameters()->get('pgmCallSpec');
            $lib = $pgmCallSpec['library-name'];
            $usrlibl = $toolkitInstance->ClCommandWithOutput('RTVJOBA USRLIBL(?)');
            $usrlibl_array = explode(' ', $usrlibl['USRLIBL']);
            if (!in_array($lib, $usrlibl_array)) {
                $addlible = 'ADDLIBLE ' . $lib;
                $toolkitInstance->CLCommand($addlible);
                if (array_key_exists('libraries', $pgmCallSpec)) {
                    foreach ($pgmCallSpec['libraries'] as $key => $param) {
                        if ($param === $lib || in_array($lib, $usrlibl_array)) {
                            continue;
                        }
                        $toolkitInstance->CLCommand('ADDLIBLE ' . $param);
                    }
                }
            }
        } catch (\Exception $error) {
            $response->setContent($error);
            $e->stopPropagation();
        }

        return $response;
    }
}
