<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;

return [
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'application' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'         => __DIR__ . '/../view/layout/padrao.phtml',
            'base/index/index'      => __DIR__ . '/../view/base/index/index.phtml',
            'error/404'             => __DIR__ . '/../view/error/404.phtml',
            'error/index'           => __DIR__ . '/../view/error/index.phtml',
            'paginas/sobre'         => __DIR__ . '/../view/application/paginas/sobre.phtml',
            'paginas/inicio'        => __DIR__ . '/../view/application/paginas/inicio.phtml',
            'paginas/contato'       => __DIR__ . '/../view/application/paginas/contato.phtml',
            'paginas/portifolio'    => __DIR__ . '/../view/application/paginas/portifolio.phtml',
            'paginas/depoimento'    => __DIR__ . '/../view/application/paginas/depoimento.phtml',
            'paginas/login'         => __DIR__ . '/../view/application/paginas/login.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [ 
            'ViewJsonStrategy',
        ],
    ],
];
