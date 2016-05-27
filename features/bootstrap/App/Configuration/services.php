<?php

/**
 * This file is part of silvam_filipe/song-notebook package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Slick\Configuration\Configuration;
use Slick\Di\Definition\ObjectDefinition;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\PhpEnvironment\Response;
use Slick\Http\Server;
use Slick\Mvc\Dispatcher;
use Slick\Mvc\Http\UrlRewrite;
use Slick\Mvc\Renderer;
use Slick\Mvc\Router;
use Slick\Mvc\Http\Session;

/**
 * @var $this \Slick\Mvc\Application
 */

$config = Configuration::get('settings');
$templatePath = $config->get('template.path');

/**
 * Default DI services definitions
 */
$services = [];

// ------------------------------------
// Default session driver
// ------------------------------------
$services['session'] = function() use ($config) {
    $options = $config->get(
        'session',
        [
            'driver' => \Slick\Http\Session::DRIVER_SERVER,
            'options' => []
        ]
    );
    $session = new \Slick\Http\Session($options);
    return $session->initialize();
};

// ------------------------------------
// HTTP request and response objects
// ------------------------------------
$services['request'] = ObjectDefinition::create(Request::class);
$services['response'] = ObjectDefinition::create(Response::class);

// ------------------------------------
// Middleware
// ------------------------------------
$services['session.middleware']     = ObjectDefinition::create(Session::class);
$services['url.rewrite.middleware'] = ObjectDefinition::create(UrlRewrite::class);
$services['router.middleware']      = ObjectDefinition::create(Router::class)
    ->setMethod(
        'setRouteFile',
        [__DIR__.'/routes.yml']
    );
$services['dispatcher.middleware']  = ObjectDefinition::create(Dispatcher::class);
$services['renderer.middleware']    = ObjectDefinition::create(Renderer::class)
    ->setMethod(
        'addTemplatePath',
        [$templatePath]
    );

// ------------------------------------
// HTTP server middleware stack
// ------------------------------------
$services['middleware.runner'] = ObjectDefinition::create(Server::class)
    ->setConstructArgs(['@request', '@response'])
    ->setMethod('add', ['@session.middleware'])
    ->setMethod('add', ['@url.rewrite.middleware'])
    ->setMethod('add', ['@router.middleware'])
    ->setMethod('add', ['@dispatcher.middleware'])
    ->setMethod('add', ['@renderer.middleware']);

return $services;