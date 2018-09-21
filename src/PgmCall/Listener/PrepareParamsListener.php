<?php
/**
 *
 * ibmi-tools (https://github.com/fabiopellati/ibmi-tools)
 *
 * @link      https://github.com/fabiopellati/ibmi-tools
 * @copyright Copyright (c) 2017-2018 Fabio Pellati (https://github.com/fabiopellati)
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace IbmiTools\PgmCall\Listener;

use IbmiTools\Exception\InvalidParamException;
use IbmiTools\Exception\RuntimeException;
use MessageExchangeEventManager\Actuator\ActuatorRunAwareInterface;
use MessageExchangeEventManager\Event\Event;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class PrepareParamsListener
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
            $actuatorRunOptions = $request->getParameters()->get('actuatorRunOptions');
            $toolkitInstance = $request->getParameters()->get('toolkitInstance');
            $pgmCallSpec = $request->getParameters()->get('pgmCallSpec');
            $inputParams = [];
            foreach ($pgmCallSpec['params'] as $key => $param) {
                $value = $this->getParamValue($actuatorRunOptions, $key, $param);
                try {
                    $this->prepareParam($param, $value, $toolkitInstance, $inputParams);
                } catch (\Exception $error) {
                    $response->setError($error->getMessage(), $error->getCode());
                    $e->stopPropagation();
                }
            }
            $request->getParameters()->set('inputParams', $inputParams);
        } catch (\Exception $error) {
            $response->setContent($error);
            $e->stopPropagation();
        }

        return $response;
    }

    /**
     * @param $param            array
     * @param $value
     * @param $toolkit_instance Toolkit
     *
     * @param $paramsIn
     *
     * @return bool
     * @throws \IbmiTools\Exception\InvalidParamException
     * @throws \IbmiTools\Exception\RuntimeException
     */
    protected function prepareParam($param, $value, $toolkit_instance, &$paramsIn)
    {
        try {
            $io = $this->getIo($param);
            $comment = $param['comment'];
            $name = $param['name'];
            $is_char = $this->paramChar($param);
            if (is_array($is_char)) {
                $paramsIn[] = $toolkit_instance->AddParameterChar($io, $is_char[0], $comment, $name, $value);

                return true;
            }
            $is_pack_dec = $this->paramPackDec($param);
            if (is_array($is_pack_dec)) {
                $paramsIn[] = $toolkit_instance->AddParameterPackDec($io, $is_pack_dec[0], $is_pack_dec[1],
                                                                     $comment, $name, $value);
                return true;
            }
            $is_size = $this->paramSize($param);
            if (is_array($is_size)) {
                $paramsIn[] = $toolkit_instance->AddParameterSize($io, $is_size[0], $is_size[1],
                                                                  $comment, $name, $value);

                return true;
            }

        } catch (\Exception $e) {
            throw new  RuntimeException($e->getMessage(), $e->getCode());
        }
        throw new InvalidParamException('param type unattended ' . $param['name']);

    }

    /**
     * @param $param
     *
     * @return string
     * @throws \IbmiTools\Exception\InvalidParamException
     */
    protected function getIo($param)
    {
        switch (strtolower($param['io'])) {
            case 'i':
            case 'in':
                return 'IN';
                break;
            case 'o':
            case 'out':
                return 'OUT';
                break;
            case 'io':
            case 'b':
            case 'both':
                return 'BOTH';
                break;
        }
        throw new InvalidParamException(vsprintf('%s: rpg param io %s unattended', [$param['name'], $param['io']]));
    }

    /**
     * @param $param
     *
     * @return bool|array
     */
    protected function paramPackDec($param)
    {
        $patterns = [
            '#^([1-9]{1}[0-9]{0,4})(P)(,|\s)([0-9]{1,4})$#',
        ];
        $match = $this->paramCheckPatterns($param, $patterns);
        if (is_array($match)) {

            switch ($match['index']) {
                case 0:
                    return [$match['match'][1], $match['match'][3]];
                    break;
                case 1:
                    return [$match['match'][1], $match['match'][4]];
                    break;
            }
        }

        return false;
    }

    /**
     * @param $param
     *
     * @return bool|array
     */
    protected function paramSize($param)
    {
        $patterns = [
            '#^([1-9]{1}[0-9]{0,4})(S)(,|\s)([0-9]{1,4})$#',
        ];
        $match = $this->paramCheckPatterns($param, $patterns);
        if (is_array($match)) {

            switch ($match['index']) {
                case 0:
                    return [$match['match'][1], $match['match'][3]];
                    break;
                case 1:
                    return [$match['match'][1], $match['match'][4]];
                    break;
            }
        }

        return false;
    }

    /**
     * @param $param
     *
     * @return bool|array
     */
    protected function paramChar($param)
    {
        $patterns = [
            '#^([1-9]{1}[0-9]{0,4})$#',
            '#^([1-9]{1}[0-9]{0,4})(A)$#',
        ];
        $match = $this->paramCheckPatterns($param, $patterns);
        if (is_array($match)) {
            return [$match['match'][1]];
        }

        return false;
    }

    /**
     * verifica se il parametro è in match con i patterns passati per parametro
     *
     * se trova un match ritorna un array con l'indice del pattern macthato ed il match stesso
     *
     * @param $param
     * @param $patterns
     *
     * @return bool|array
     * @internal param $match
     *
     */
    protected function paramCheckPatterns($param, $patterns)
    {
        foreach ($patterns as $key => $pattern) {
            $match = [];
            $test = (preg_match($pattern, $param['type'], $match) > 0);
            if ($test) {
                return ['index' => $key, 'match' => $match];
            }

        }

        return false;
    }

    /**
     * cerca il valore del parametro nei parametri della richiesta
     * se non lo trova tenta di recuperarlo da eventuali alias configurati nello spec del parametro
     *
     * @param $actuatorRunOptions
     * @param $key
     *
     * @return mixed
     */
    protected function getParamValue($actuatorRunOptions, $key, $param)
    {
        $value = $actuatorRunOptions[$key];
        if (empty($value)) {
            $value = $actuatorRunOptions[strtolower($key)];
        }
        if (empty($value) && array_key_exists('alias', $param)) {
            foreach ($param['alias'] as $alias) {
                $value = $actuatorRunOptions[$alias];
                if (!empty($value)) {
                    break;
                }
            }
        }

        return $value;
    }
}
