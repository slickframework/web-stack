<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http\Server;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Message;
use Slick\Http\Message\Response;

/**
 * RouterMiddleware
 *
 * @package Slick\WebStack\Http\Server
 */
class RouterMiddleware implements MiddlewareInterface
{

    /**
     * Creates a Router Middleware
     *
     * @param RouterContainer $routerContainer
     */
    public function __construct(private RouterContainer $routerContainer)
    {
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $matcher = $this->routerContainer->getMatcher();
        $route = $matcher->match($request);

        if (!$route instanceof Route) {
            return $this->handleFailedRoute($matcher->getFailedRoute());
        }

        $request = $request->withAttribute('route', $route);
        return $handler->handle($request);
    }

    private function handleFailedRoute(Route $failedRoute): Response|Message
    {
        return match ($failedRoute->failedRule) {
            'Aura\Router\Rule\Allows' => (new Response(405))
                ->withHeader('Allow', $failedRoute->allows),
            'Aura\Router\Rule\Accepts' => new Response(406),
            default => new Response(404),
        };
    }
}
