<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc;

use Aura\Router\Matcher;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Mvc\Router\RouteBuilder;

/**
 * HTTP Request Router
 *
 * @package Slick\Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Router extends AbstractMiddleware implements MiddlewareInterface
{

    /**
     * @var RouterContainer
     */
    protected $routerContainer;

    /**
     * @var Matcher
     */
    protected $matcher;

    /**
     * @var RouteBuilder
     */
    protected $routeBuilder;

    /**
     * @var string
     */
    protected $routeFile;

    /**
     * Returns route container
     *
     * @return RouterContainer
     */
    public function getRouterContainer()
    {
        if (null === $this->routerContainer) {
            $this->setRouterContainer(new RouterContainer());
            $this->routerContainer
                ->setMapBuilder([$this->getRouteBuilder(), 'build']);
        }
        return $this->routerContainer;
    }

    /**
     * Sets router container
     *
     * @param RouterContainer $routerContainer
     *
     * @return self|$this|Router
     */
    public function setRouterContainer(RouterContainer $routerContainer)
    {
        $this->routerContainer = $routerContainer;
        return $this;
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
        $route = $this->getMatcher()->match($request);
        $request = $request->withAttribute('route', $route);
        return $this->executeNext($request, $response);
    }

    /**
     * Gets route matcher for this router
     *
     * @return Matcher
     */
    public function getMatcher()
    {
        if (null === $this->matcher) {
            $this->setMatcher($this->getRouterContainer()->getMatcher());
        }
        return $this->matcher;
    }

    /**
     * Sets the route matcher for current used in Router::handle()
     *
     * @param Matcher $matcher
     *
     * @return $this|self|Router
     */
    public function setMatcher(Matcher $matcher)
    {
        $this->matcher = $matcher;
        return $this;
    }

    /**
     * Get route builder
     *
     * @return RouteBuilder
     */
    public function getRouteBuilder()
    {
        if (null == $this->routeBuilder) {
            $this->setRouteBuilder(new RouteBuilder($this->getRouteFile()));
        }
        return $this->routeBuilder;
    }

    /**
     * Set route builder
     *
     * @param RouteBuilder $routeBuilder
     * @return Router
     */
    public function setRouteBuilder($routeBuilder)
    {
        $this->routeBuilder = $routeBuilder;
        return $this;
    }

    /**
     * Get route file full path
     *
     * @return string
     */
    public function getRouteFile()
    {
        return $this->routeFile;
    }

    /**
     * Set route definition file
     *
     * @param string $routeFile
     * @return Router
     */
    public function setRouteFile($routeFile)
    {
        $this->routeFile = $routeFile;
        return $this;
    }
}