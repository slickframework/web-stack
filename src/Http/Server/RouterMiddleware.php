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
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Response;

/**
 * RouterMiddleware
 *
 * @package Slick\WebStack\Http\Server
 */
class RouterMiddleware implements MiddlewareInterface
{
    /**
     * @var RouterContainer
     */
    private $routerContainer;

    /**
     * Creates a Router Middleware
     *
     * @param RouterContainer $routerContainer
     */
    public function __construct(RouterContainer $routerContainer)
    {
        $this->routerContainer = $routerContainer;
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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $matcher = $this->routerContainer->getMatcher();
        $route = $matcher->match($request);

        if (!$route instanceof Route) {
            return $this->handleFailedRoute($matcher->getFailedRoute());
        }

        $request = $request->withAttribute('route', $route);
        return $handler->handle($request);
    }

    private function handleFailedRoute(Route $failedRoute)
    {
        switch ($failedRoute->failedRule) {
            case 'Aura\Router\Rule\Allows':
                $response = (new Response(405))
                    ->withHeader('Allow', $failedRoute->allows);
                break;

            case 'Aura\Router\Rule\Accepts':
                $response = new Response(406);
                break;

            default:
                $response = new Response(404);
        }
        return $response;
    }

}