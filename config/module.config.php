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
use Zend\Validator\ValidatorChain;

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
        'IbmiTools\\PgmCall\\Listener\\ValidatorErrorListener'            => [
            ValidatorChain::class,
        ],
        //        'IbmiTools\\PgmCall\\Listener\\CacheListener'                     => [
        //            ValidatorChain::class,
        //        ],
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
                'IbmiTools\\PgmCall\\Listener\\SetToolkitListener'    => 'IbmiTools\\PgmCall\\Listener\\SetToolkitInstanceDefaultListener',
                'IbmiTools\\PgmCall\\Listener\\SetResultsetListener'  => 'IbmiTools\\PgmCall\\Listener\\SetResultsetDefaultListener',
                'IbmiTools\\PgmCall\\Listener\\SetHydratorListener'   => 'IbmiTools\\PgmCall\\Listener\\SetHydratorDefaultListener',
                'IbmiTools\\PgmCall\\Listener\\PrepareParamsListener' => 'IbmiTools\\PgmCall\\Listener\\PrepareParamsListener',
                'IbmiTools\\PgmCall\\Listener\\AddLibrariesListener'  => 'IbmiTools\\PgmCall\\Listener\\AddLibrariesListener',
                'IbmiTools\\PgmCall\\Listener\\PgmCallListener'       => 'IbmiTools\\PgmCall\\Listener\\PgmCallListener',
                'IbmiTools\\PgmCall\\Listener\\ErrorListener'         => 'IbmiTools\\PgmCall\\Listener\\ErrorListener',
                'IbmiTools\\PgmCall\\Listener\\HydrateResultListener' => 'IbmiTools\\PgmCall\\Listener\\HydrateResultListener',
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
            'IbmiTools\\ToolkitInstance\\Default'                 => 'IbmiTools\\ToolkitInstance\\ToolkitInstanceFactory',
            'IbmiTools\\PgmCall'                                  => 'IbmiTools\\PgmCallActuatorFactory',
            //            'IbmiTools\\PgmCall\\Listener\\CacheListener'         => InvokableFactory::class,
            'IbmiTools\\PgmCall\\Listener\\PrepareParamsListener' => InvokableFactory::class,
            'IbmiTools\\PgmCall\\Listener\\AddLibrariesListener'  => InvokableFactory::class,
            'IbmiTools\\PgmCall\\Listener\\PgmCallListener'       => InvokableFactory::class,
            'IbmiTools\\PgmCall\\Listener\\ErrorListener'         => InvokableFactory::class,
            'IbmiTools\\PgmCall\\Listener\\HydrateResultListener' => InvokableFactory::class,
        ],
    ],
];
