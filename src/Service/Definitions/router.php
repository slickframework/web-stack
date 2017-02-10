<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Services\Definitions;

use Aura\Router\RouterContainer;
use Slick\Configuration\Configuration;
use Slick\Di\ContainerInterface;
use Slick\Di\Definition\ObjectDefinition;
use Slick\WebStack\Http\Router\Builder\RouteFactory;
use Slick\WebStack\Http\Router\RouteBuilder;
use Slick\WebStack\Http\RouterMiddleware;
use Symfony\Component\Yaml\Parser;

$services = [];

$services['routes.file'] = __DIR__.'/routes.yml';
$services['router.middleware'] = ObjectDefinition::create(RouterMiddleware::class)
    ->with('@router.container');

$services['route.builder'] = ObjectDefinition::create(RouteBuilder::class)
    ->with(
        '@routes.file',
        '@routes.yml.parser',
        '@route.factory'
    );
$services['router.container'] = function (ContainerInterface $container) {
    /** @var RouteBuilder $builder */
    $builder = $container->get('route.builder');
    $routerContainer = new RouterContainer();
    $builder->register($routerContainer);
    return $routerContainer;
};
$services['routes.yml.parser']= ObjectDefinition::create(Parser::class);
$services['route.factory'] = ObjectDefinition::create(RouteFactory::class);

return $services;