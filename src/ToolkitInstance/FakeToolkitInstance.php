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

use ToolkitApi\Toolkit;

class FakeToolkitInstance
    extends Toolkit
{
    public static $fakeLog = [];

    static function AddParameterChar($io, $size, $comment, $varName = '', $value = '', $varying = 'off', $dimension = 0,
                                     $by = '', $isArray = false, $ccsidBefore = '', $ccsidAfter = '', $useHex = false)
    {
        self::$fakeLog[] = [__METHOD__, func_get_args()];

        return [__CLASS__, __METHOD__];
    }

    static function AddParameterPackDec($io, $length, $scale, $comment, $varName = '', $value = '', $dimension = 0)
    {
        self::$fakeLog[] = [__METHOD__, func_get_args()];

        return [__CLASS__, __METHOD__];
    }

    public function isError()
    {
        self::$fakeLog[] = [__METHOD__, func_get_args()];

        return false;
    }

    public function getErrorMsg()
    {
        self::$fakeLog[] = [__METHOD__, func_get_args()];

        return 'FakeToolkit ' . __METHOD__;
    }

    public function getErrorCode()
    {
        self::$fakeLog[] = [__METHOD__, func_get_args()];

        return 500;
    }

    public function ClCommandWithOutput($command)
    {
        self::$fakeLog[] = [__METHOD__, func_get_args()];

        return ['fakeToolkit ', __METHOD__];
    }

    /**
     * if passing an existing resource and naming, don't need the other params.
     *
     * @param        $databaseNameOrResource
     * @param string $userOrI5NamingFlag 0 = DB2_I5_NAMING_OFF or 1 = DB2_I5_NAMING_ON
     * @param string $password
     * @param string $transportType      (http, ibm_db2, odbc)
     * @param bool   $isPersistent
     *
     * @throws \Exception
     */
    public function __construct($databaseNameOrResource, $userOrI5NamingFlag = '0', $password = '', $transportType = '', $isPersistent = false)
    {
        self::$fakeLog[] = [__METHOD__, func_get_args()];
    }

    /**
     * Send any XML to XMLSERVICE toolkit. The XML doesn't have to represent a program.
     * Was protected; made public to be usable by applications.
     *
     * @param      $inputXml
     * @param bool $disconnect
     *
     * @return string
     * @throws \Exception
     */
    public function ExecuteProgram($inputXml, $disconnect = false)
    {
        self::$fakeLog[] = [__METHOD__, func_get_args()];

        return 'fakeToolkit ' . __METHOD__;

    }

    public function CLCommand($command, $exec = '')
    {
        self::$fakeLog[] = [__METHOD__, func_get_args()];
    }

}