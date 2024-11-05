<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infrastructure\Http\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Slick\Di\Container;
use Slick\Di\Definition\ObjectDefinition;
use Slick\WebStack\Infrastructure\Http\Routing\RequestContext;
use Slick\WebStack\Infrastructure\Http\Routing\RoutesAttributeClassLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

$services = [];

$cacheDirectory = sys_get_temp_dir() . '/cache/routes';
$path = APP_ROOT . '/src/UserInterface';
$userInterfaceDirectory = $path;
if (!is_dir($cacheDirectory)) {
    mkdir($cacheDirectory, 0777, true);
}

$services['request.context'] = function (Container $container) {
    $context = new RequestContext();
    $request = $container->get('http.request');
    return $context->fromPsrRequest($request);
};

$services['routes.attribute.loader'] = function () use ($userInterfaceDirectory) {
    return new AttributeDirectoryLoader(
        new FileLocator($userInterfaceDirectory),
        new RoutesAttributeClassLoader()
    );
};

$services[RouterInterface::class] = '@router';
$services['router'] = function (Container $container) use ($userInterfaceDirectory, $cacheDirectory) {
    $router = new Router(
        loader: $container->get('routes.attribute.loader'),
        resource: $userInterfaceDirectory,
        context: $container->get('request.context')
    );

    //$router->setOptions(['cache_dir' => $cacheDirectory]);
    return $router;
};

$services[UrlMatcherInterface::class] = '@url.matcher';
$services['url.matcher'] = function (Container $container) {
    /** @var Router $router */
    $router = $container->get('router');
    return $router->getMatcher();
};

$services[UrlGeneratorInterface::class] = '@url.generator';
$services['url.generator'] = function (Container $container) {
    /** @var Router $router */
    $router = $container->get('router');
    return $router->getGenerator();
};

return $services;
