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

use IbmiTools\Exception\RuntimeException;
use ToolkitApi\Toolkit;

class FakeToolkitInstance
    extends Toolkit
{
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
        throw new RuntimeException('si sta usando la versione Fake del toolkit, nessuna risposta prevista', 500);
    }
}