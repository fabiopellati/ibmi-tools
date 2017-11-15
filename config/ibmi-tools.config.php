<?php
/**
 *
 * ibmi-tools (https://github.com/fabiopellati/ibmi-tools)
 *
 * @link      https://github.com/fabiopellati/ibmi-tools
 * @copyright Copyright (c) 2017-2017 Fabio Pellati (https://github.com/fabiopellati)
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace IbmiTools;

use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;

return [
    ConfigAbstractFactory::class => [
    ],
    'service_manager'            => [
        'factories' => [
            'Application\\MyPngCallExample'         => 'IbmiTools\\PgmCallActuatorFactory',
            'IbmiTools\\Toolkit\\Instance\\Default' => 'IbmiTools\\Toolkit\\ToolkitInstanceFactory',
        ],
    ],
    'example'                    => [
        'Example\\PgmCall\\Listener\\PgmCallContentErrorListener' => [
            'param-error-code'    => 'FLGERR',
            'param-error-message' => 'TXTERR',
        ],
    ],
    'ibmi-tools'                 => [
        'pgm-call-default' => [
            'cache'  => 'key_for_zend_cache',
            'params' => [
                /**
                 * elenco di parametri template ripetitivi
                 * se inseriti come nella sezione parametri del pgm-call possono essere richiamati inserendo
                 * in pgm-call/params la chiabve in questo modo
                 * 'nome_parametro'=>'chiave_parametro_default'
                 */
            ],
        ],
        'toolkit'          => [
            'instance-config' => [
                'IbmiTools\\ToolkitInstance\\Default' => [
                    'database' => '*LOCAL',
                    'user'     => 'QPGMR',
                    'password' => '',
                ],
            ],
        ],
        'pgm-call'         => [
            'Application\\MyPngCallExample' => [
                'library-name' => 'W2WSTD_OBJ',
                'program-name' => 'VENU046',
                'listeners'    => [
                ],
                'params'       => [
                    'CODAZI' => [
                        'name'    => 'CODAZI',
                        'alias'   => [],
                        'type'    => '3',
                        'comemnt' => 'Codice azienda STD o GEC o STD',
                        'io'      => 'I',
                    ],
                ],
            ],
        ],
    ],
];
