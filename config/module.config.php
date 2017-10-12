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

use MessageExchangeEventManager\Resultset\Resultset;
use MessageExchangeEventManager\Resultset\ResultsetHydrator;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    ConfigAbstractFactory::class => [
        'IbmiTools\\PgmCall\\Listener\\SetToolkitInstanceDefaultListener' => [
            'IbmiTools\\ToolkitInstance\\Default',
        ],
        'IbmiTools\\PgmCall\\Listener\\SetResultsetDefaultListener'       => [
            Resultset::class,
        ],
        'IbmiTools\\PgmCall\\Listener\\SetHydratorDefaultListener'        => [
            ResultsetHydrator::class,
        ],
    ],
    'ibmi-tools'                 => [
        'toolkit-instance' => [
            'IbmiTools\\ToolkitInstance\\Default' => [
                'database' => '*LOCAL',
                'user'     => '',
                'password' => '',
            ],
        ],
        'pgm-call-default' => [
            'listeners' => [
                'IbmiTools\\PgmCall\\Listener\\SetToolkitListener'           => 'IbmiTools\\PgmCall\\Listener\\SetToolkitInstanceDefaultListener',
                'IbmiTools\\PgmCall\\Listener\\SetResultsetListener'         => 'IbmiTools\\PgmCall\\Listener\\SetResultsetDefaultListener',
                'IbmiTools\\PgmCall\\Listener\\SetHydratorListener'          => 'IbmiTools\\PgmCall\\Listener\\SetHydratorDefaultListener',
                'IbmiTools\\PgmCall\\Listener\\PgmCallPrepareParamsListener' => 'IbmiTools\\PgmCall\\Listener\\PgmCallPrepareParamsListener',
                'IbmiTools\\PgmCall\\Listener\\PgmCallAddLibrariesListener'  => 'IbmiTools\\PgmCall\\Listener\\PgmCallAddLibrariesListener',
                'IbmiTools\\PgmCall\\Listener\\PgmCallListener'              => 'IbmiTools\\PgmCall\\Listener\\PgmCallListener',
                'IbmiTools\\PgmCall\\Listener\\PgmCallErrorListener'         => 'IbmiTools\\PgmCall\\Listener\\PgmCallErrorListener',
                'IbmiTools\\PgmCall\\Listener\\PgmCallHydrateListener'       => 'IbmiTools\\PgmCall\\Listener\\PgmCallHydrateListener',
            ],
            'params'    => [
            ],
        ],
        /**
         * pgm-call place here every single pgm-call spec
         */
        'pgm-call'         => [],
    ],
    'service_manager'            => [
        'abstract_factories' => [
            ConfigAbstractFactory::class,
        ],
        'factories'          => [
            'IbmiTools\\ToolkitInstance\\Default'               => 'IbmiTools\\ToolkitInstance\\ToolkitInstanceFactory',
            'IbmiTools\\PgmCall'                                => 'IbmiTools\\PgmCallActuatorFactory',
            'IbmiTools\\Listener\\CacheListener'                => InvokableFactory::class,
            'IbmiTools\\Listener\\PgmCallPrepareParamsListener' => InvokableFactory::class,
            'IbmiTools\\Listener\\PgmCallAddLibrariesListener'  => InvokableFactory::class,
            'IbmiTools\\Listener\\PgmCallListener'              => InvokableFactory::class,
            'IbmiTools\\Listener\\PgmCallErrorListener'         => InvokableFactory::class,
            'IbmiTools\\Listener\\PgmCallHydrateListener'       => InvokableFactory::class,
        ],
    ],
];
