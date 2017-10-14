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
class InvalidParamException
    extends \Exception
{
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        $code = ($code === 0) ? 417 : $code;
        parent::__construct($message, $code, $previous);
    }

}