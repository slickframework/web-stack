<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service\Definitions;

use Slick\Configuration\Configuration;
use Slick\Di\ContainerInterface;
use Slick\Di\Definition\ObjectDefinition;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\PhpEnvironment\Response;
use Slick\Http\Server;
use Slick\Http\Session;
use Slick\Http\SessionDriverInterface;
use Slick\WebStack\Http\SessionMiddleware;
use Slick\WebStack\Http\UrlRewriterMiddleware;
use Slick\WebStack\Service\FlashMessages;

$services = [];

$services[SessionDriverInterface::class] =  Session::DRIVER_NULL;

// SESSION DRIVER
$services['session.driver'] = function (ContainerInterface $container) {
    $session = new Session(
        ['driver' => $container->get(SessionDriverInterface::class)]
    );
    return $session->initialize();
};

// FLASH MESSAGES
$services['flash.messages'] = function (ContainerInterface $container) {
    return new FlashMessages($container->get('session.driver'));
};

// REQUEST/RESPONSE objects
$services['server.request']  = function () {return new Request(); };
$services['server.response'] = function () {return new Response(); };

// MIDDLEWARE
$services['session.middleware'] = ObjectDefinition::create(SessionMiddleware::class)
    ->with('@session.driver');
$services['url.rewriter.middleware'] = ObjectDefinition
    ::create(UrlRewriterMiddleware::class);

$services['middleware.server'] = ObjectDefinition::create(Server::class)
    ->with('@server.request', '@server.response')

    ->call('add')->with('@session.middleware')
    ->call('add')->with('@url.rewriter.middleware')
    ->call('add')->with('@router.middleware')
    ->call('add')->with('@dispatcher.middleware')
    ->call('add')->with('@renderer.middleware')
;

return $services;