<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Infrastructure\Http\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Slick\Configuration\ConfigurationInterface;
use Slick\Di\Container;
use Slick\Di\Definition\ObjectDefinition;
use Slick\WebStack\Infrastructure\Http\Routing\MultiPathAttributeDirectoryLoader;
use Slick\WebStack\Infrastructure\Http\Routing\RequestContext;
use Slick\WebStack\Infrastructure\Http\Routing\RoutesAttributeClassLoader;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Routing\RouterInterface;

$services = [];

$services['routingBasePath'] = $_ENV['ROUTING_BASE_PATH'] ?? '';

$services['request.context'] = function (Container $container) {
    $context = new RequestContext();
    $request = $container->get('http.request');
    $requestContext = $context->fromPsrRequest($request);
    $baseUrl = $container->get('routingBasePath');
    if (!empty($baseUrl)) {
        $requestContext->setBaseUrl($baseUrl);
    }
    return $requestContext;
};

$services['routes.attribute.loader'] = function (Container $container) {
    /** @var ConfigurationInterface $settings */
    $settings = $container->get('settings');
    $resourcesPath = $settings->get('router.resources_path', APP_ROOT . '/src/UserInterface');
    $paths = is_array($resourcesPath) ? $resourcesPath : [$resourcesPath];

    return new MultiPathAttributeDirectoryLoader($paths);
};

$services[RouterInterface::class] = '@router';
$services['router'] = function (Container $container) {
    /** @var ConfigurationInterface $settings */
    $settings = $container->get('settings');
    $resourcesPath = $settings->get('router.resources_path', APP_ROOT . '/src/UserInterface');
    $primaryPath = is_array($resourcesPath) ? $resourcesPath[0] : $resourcesPath;

    $router = new Router(
        loader: $container->get('routes.attribute.loader'),
        resource: $primaryPath,
        context: $container->get('request.context')
    );

    if ($settings->get('router.cache.enabled', false)) {
        $cacheDirectory = $settings->get('router.cache.directory', sys_get_temp_dir() . '/cache/routes');
        if (!is_dir($cacheDirectory)) {
            mkdir($cacheDirectory, 0777, true);
        }
        $router->setOptions(['cache_dir' => $cacheDirectory]);
    }

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