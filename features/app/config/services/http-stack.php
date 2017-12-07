<?php

/**
 * Application HTTP middleware stack services
 */

namespace Features\App\Config\Services;

use Aura\Router\RouterContainer;
use Slick\Di\Container;
use Slick\Di\Definition\ObjectDefinition;
use Slick\Http\Message\Response;
use Slick\Http\Server\MiddlewareStack;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Controller\ContextCreator;
use Slick\WebStack\Dispatcher\ControllerDispatchInflector;
use Slick\WebStack\Dispatcher\ControllerInvoker;
use Slick\WebStack\Http\Server\DispatcherMiddleware;
use Slick\WebStack\Http\Server\FlashMessagesMiddleware;
use Slick\WebStack\Http\Server\RendererMiddleware;
use Slick\WebStack\Http\Server\RouterMiddleware;
use Slick\WebStack\Renderer\ViewInflector;
use Slick\WebStack\Router\Builder\RouteFactory;
use Slick\WebStack\Router\RouteBuilder;
use Slick\WebStack\Service\FlashMessages;
use Slick\WebStack\Service\UriGenerator;
use Symfony\Component\Yaml\Parser;

$services = [];


//-----------------------------------
//  Middleware stack
//-----------------------------------
$handler = function () {
    return new Response(200, 'Features application!');
};
$services[MiddlewareStack::class] = ObjectDefinition::create(MiddlewareStack::class)
    ->with([])
    // Here you can add your middleware or arrange the order of you stack
    ->call('push')->with('@router.middleware')
    ->call('push')->with('@flash.messages.middleware')
    ->call('push')->with('@dispatcher.middleware')
    ->call('push')->with('@renderer.middleware')

    // This is the default response
    ->call('push')->with($handler)
;


//-----------------------------------
//  Router Middleware services
//-----------------------------------
$services['router.container'] = ObjectDefinition::create(RouterContainer::class);
$services['router.middleware'] = function (Container $container) {
    $routeBuilder = new RouteBuilder(
        dirname(__DIR__).'/routes.yml',
        new Parser(),
        new RouteFactory()
    );
    $routerContainer = $container->get('router.container');
    $routeBuilder->register($routerContainer);
    return new RouterMiddleware($routerContainer);
};

//-----------------------------------
//  Flash Messages Middleware services
//-----------------------------------
$services[FlashMessages::class] = '@flash.messages.service';
$services['flash.messages.service'] = ObjectDefinition::create(FlashMessages::class)
    ->with('@session.driver');
$services['flash.messages.middleware'] = ObjectDefinition::create(FlashMessagesMiddleware::class)
    ->with('@flash.messages.service');

//-----------------------------------
//  Dispatcher Middleware services
//-----------------------------------
$services['uri.generator'] = function (Container $container) {
    $uriGenerator = new UriGenerator();
    $uriGenerator->addTransformer(
        new UriGenerator\Transformer\RouterPathTransformer($container->get('router.container'))
    );
    $uriGenerator->addTransformer(new UriGenerator\Transformer\FullUrlTransformer());
    $uriGenerator->addTransformer(new UriGenerator\Transformer\BasePathTransformer());
    return $uriGenerator;
};

$services['dispatcher.middleware'] = function (Container $container) {
    $inflector = new ControllerDispatchInflector();
    $invoker = new ControllerInvoker($container);
    $contextCreator = new ContextCreator($container->get('uri.generator'));
    return new DispatcherMiddleware($inflector, $invoker, $contextCreator);
};

//-----------------------------------
//  Renderer Middleware services
//-----------------------------------

$services['renderer.middleware'] = function (Container $container) {
    return new RendererMiddleware(
        $container->get(TemplateEngineInterface::class),
        new ViewInflector('twig')
    );
};
return $services;
