<?php
/**
 *
 * ibmi-tools (https://github.com/fabiopellati/ibmi-tools)
 *
 * @link      https://github.com/fabiopellati/ibmi-tools
 * @copyright Copyright (c) 2017-2017 Fabio Pellati (https://github.com/fabiopellati)
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace IbmiTools\Exception;
class ToolkitException
    extends \Exception
{

    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $message = $this->parseMessage($code, $message);

        return parent::__construct($message, $code, $previous);
    }

    /**
     * @param $code
     *
     * @return mixed
     */
    protected function parseMessage($code, $message)
    {
        switch ($code) {
            case 'MCH3401':
                return 'errore di sistema oggetto non trovato';
                break;
            default:
                return $message;
                break;
        }
    }

}