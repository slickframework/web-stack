<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http;

use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;

/**
 * Router Middleware
 *
 * @package Slick\WebStack\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
final class RouterMiddleware extends AbstractMiddleware implements
    MiddlewareInterface
{
    /**
     * @var RouterContainer
     */
    private $routerContainer;

    /**
     * Creates Router Middleware
     *
     * @param RouterContainer $routerContainer
     */
    public function __construct(RouterContainer $routerContainer)
    {
        $this->routerContainer = $routerContainer;
    }

    /**
     * Handles a Request and updated the response
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function handle(
        ServerRequestInterface $request, ResponseInterface $response
    )
    {
        $matcher = $this->routerContainer
            ->getMatcher();

        $route = $matcher->match($request);

        if (!$route) {
            return $this->handleFailedRoute(
                $matcher->getFailedRoute(),
                $response
            );
        }

        $request = $request->withAttribute('route', $route);
        return $this->executeNext($request, $response);
    }

    /**
     * Sets the response for failed route
     *
     * @param Route             $failedRoute
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    private function handleFailedRoute(
        Route $failedRoute,
        ResponseInterface $response
    ) {
        switch ($failedRoute->failedRule) {
            case 'Aura\Router\Rule\Allows':
                $response = $response
                    ->withStatus(405)
                    ->withHeader('allow', $failedRoute->allows);
                break;

            case 'Aura\Router\Rule\Accepts':
                $response = $response
                    ->withStatus(406);
                break;

            default:
                $response = $response
                    ->withStatus(404);
        }

        return $response;
    }
}