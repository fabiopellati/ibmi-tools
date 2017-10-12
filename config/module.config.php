<?php

namespace IbmiTools;

use MessageExchangeEventManager\Resultset\Resultset;
use MessageExchangeEventManager\Resultset\ResultsetHydrator;
use Zend\ServiceManager\AbstractFactory\ConfigAbstractFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    ConfigAbstractFactory::class => [
        'IbmiTools\\PgmCall\\Listener\\SetToolkitInstanceDefaultListener' => [
            'IbmiTools\\Toolkit\\Instance\\Default',
        ],
        'IbmiTools\\PgmCall\\Listener\\SetResultsetDefaultListener'       => [
            Resultset::class,
        ],
        'IbmiTools\\PgmCall\\Listener\\SetHydratorDefaultListener'        => [
            ResultsetHydrator::class,
        ],
    ],
    'ibmi-tools'                 => [
        'toolkit'          => [
            'instance' => [],
        ],
        'pgm-call-default' => [
            'listeners' => [
                'IbmiTools\\PgmCall\\Listener\\SetToolkitListener'           => 'IbmiTools\\PgmCall\\Listener\\SetToolkitDefaultListener',
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
         * pgm-call place here every single call spec
         */
        'pgm-call'         => [],
    ],
    'service_manager'            => [
        'abstract-factory' => [
            ConfigAbstractFactory::class,
        ],
        'factories'        => [
            'IbmiTools\\Toolkit\\Instance\\Default'             => 'IbmiTools\\Toolkit\\ToolkitInstanceFactory',
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
