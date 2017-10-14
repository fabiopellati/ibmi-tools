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

use Gnc\ChainEvent\Event\EventInterface;
use IbmiTools\RunAwareInterface;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class PgmCallContentErrorListener
    extends AbstractListenerAggregate
{

    protected $param_error_code;

    protected $param_error_message;

    /**
     * @return string
     */
    public function getParamErrorCode()
    {
        return $this->param_error_code;
    }

    /**
     * @param string $param_error_code
     */
    public function setParamErrorCode($param_error_code)
    {
        $this->param_error_code = $param_error_code;
    }

    /**
     * @return string
     */
    public function getParamErrorMessage()
    {
        return $this->param_error_message;
    }

    /**
     * @param string $param_error_message
     */
    public function setParamErrorMessage($param_error_message)
    {
        $this->param_error_message = $param_error_message;
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $events
     * @param int                                      $priority
     *
     * @throws \IbmiTools\Exception\InvalidConfigurationException
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {

        $this->listeners[] = $events->attach(RunAwareInterface::EVENT_RUN_POST, [$this, 'onRunPost'], $priority);

    }

    /**
     * @param \Gnc\ChainEvent\Event\EventInterface $e
     *
     * @return \Gnc\ChainEvent\Response\Response
     */
    public function onRunPost(EventInterface $e)
    {

        $request = $e->getRequest();
        $response = $e->getResponse();
        try {

        } catch (\Exception $error) {
            $response->setContent($error);
            $e->stopPropagation();
        }

        return $response;
        if ($toolkit->isError()) {
            $response->setError($toolkit->getErrorMsg(), 500);
        }
        $result = $e->getResponse()->getContent();
        if ($result === false) {
            if ($toolkit->isError()) {
                $response->setError($toolkit->getErrorMsg(), 500);
            } else {
                $response->setError('errore non previsto del toolkit ' . $toolkit->getErrorCode() . ': ' .
                                    $toolkit->getErrorMsg(),
                                    500);
            }
        }
        $param_error_code = $this->getParamErrorCode();
        $param_error_message = $this->getParamErrorMessage();
        if (!empty(trim($result['io_param'][$param_error_code]))) {
            $response->setError($result['io_param'][$param_error_message], 500);
            $e->stopPropagation();
        }
//        switch ($result['io_param'][$param_error_code]) {
//            case 'E':
//                $response->setError($result['io_param'][$param_error_message], 500);
//                $e->stopPropagation();
//
//                break;
//        }
        return $response;
    }

}