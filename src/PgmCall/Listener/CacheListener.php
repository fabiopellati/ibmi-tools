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
use MessageExchangeEventManager\Response\Response;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;
use Zend\Validator\Barcode\AbstractAdapter;

class CacheListener
    extends AbstractListenerAggregate
{

    /**
     * @var \Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    protected $cache;

    public function __construct(AbstractAdapter $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return \Zend\Cache\Storage\Adapter\AbstractAdapter
     */
    public function getCache()
    {
        return $this->cache;
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
    public function attach(EventManagerInterface $events, $priority = 1000)
    {

        $this->listeners[] =
            $events->attach(ActuatorRunAwareInterface::EVENT_ACTUATOR_RUN, [$this, 'onRunPre'], $priority + 1000);
        $this->listeners[] =
            $events->attach(ActuatorRunAwareInterface::EVENT_ACTUATOR_RUN, [$this, 'onRunPost'], $priority - 1000);

    }

    /**
     *
     * @param \MessageExchangeEventManager\Event\Event $e
     *
     * @return \MessageExchangeEventManager\Response\ResponseInterface
     */
    public function onRunPre(Event $e)
    {
        $request = $e->getRequest();
        $response = $e->getResponse();
        try {
            $hash = $this->getHash($request);
            $response = $e->getResponse();
            if ($this->getCache()->hasItem($hash)) {
                $e->stopPropagation();
                $content = $this->getCache()->getItem($hash);
                $response->setContent($content);
            }

        } catch (\Exception $error) {
            $response->setContent($error);
            $e->stopPropagation();
        }

        return $response;
    }

    /**
     *
     * @param \MessageExchangeEventManager\Event\Event $e
     *
     * @return \MessageExchangeEventManager\Response\ResponseInterface
     */
    public function onRunPost(Event $e)
    {
        $request = $e->getRequest();
        $response = $e->getResponse();
        try {
            $hash = $this->getHash($request);
            if ($response instanceof Response && !$response->isError()) {
                $this->getCache()->setItem($hash, $response->getContent());
            }
        } catch (\Exception $error) {
            $response->setContent($error);
            $e->stopPropagation();
        }

        return $response;
    }

    /**
     * @param $request
     *
     * @return string
     */
    protected function getHash($request)
    {
        $actuatorRunOptions = $request->getParameters()->get('actuatorRunOptions');
        $toolkitInstance = $request->getParameters()->get('toolkitInstance');
        $pgmCallSpec = $request->getParameters()->get('pgmCallSpec');
        $actuatorRunOptionsString = implode('-', $actuatorRunOptions);
        $hashString = get_class($toolkitInstance) . $pgmCallSpec['library-name'] . $pgmCallSpec['program-name'] .
            $actuatorRunOptionsString;
        $hash = sha1($hashString);

        return $hash;
    }
}
