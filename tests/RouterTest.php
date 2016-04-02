<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc;

use Aura\Router\Map;
use Aura\Router\Matcher;
use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Aura\Router\Rule\RuleIterator;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slick\Mvc\Router;

/**
 * Router Test Case
 *
 * @package Slick\Tests\Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RouterTest extends TestCase
{

    /**
     * @var Router
     */
    public $router;

    /**
     * Sets the SUT router object
     */
    public function setUp()
    {
        parent::setUp();
        $this->router = new Router();
        $this->router->setRouteFile(__DIR__.'/routes.yml');
    }

    /**
     * clear SUT for next test
     */
    protected function tearDown()
    {
        $this->router = null;
        parent::tearDown();
    }

    /**
     * Should create a route builder with provided file name
     * @test
     */
    public function createARouteBuilder()
    {
        $builder = $this->router->getRouteBuilder();
        $this->assertInstanceOf(Router\RouteBuilder::class, $builder);
    }

    /**
     * Should create a route container and inject the map builder
     * @test
     */
    public function createRouteContainer()
    {
        /** @var Router\RouteBuilder $routeBuilder */
        $routeBuilder = $this->getMockBuilder(Router\RouteBuilder::class)
            ->setConstructorArgs([new Route()])
            ->getMock();
        $this->router->setRouteBuilder($routeBuilder);

        $container = $this->router->getRouterContainer();
        $this->assertInstanceOf(RouterContainer::class, $container);
    }

    /**
     * Should grab the matcher from existing route container
     * @test
     */
    public function createRouteMatcher()
    {
        $matcher = $this->getMatcherMock();
        $container = $this->getRouterContainerMock();
        $container->expects($this->once())
            ->method('getMatcher')
            ->willReturn($matcher);
        $this->router->setRouterContainer($container);

        $this->assertSame($matcher, $this->router->getMatcher());
    }

    /**
     * Should use the route matcher to match the provided request and injecting the
     * matching route in the request.
     * @test
     */
    public function handleARequest()
    {
        $route = new Route;
        /** @var ResponseInterface $response */
        $response = $this->getMock(ResponseInterface::Class);
        $request = $this->getRequestMock();
        $request->expects($this->once())
            ->method('withAttribute')
            ->with('route', $route)
            ->willReturnSelf();
        $matcher = $this->getMatcherMock();
        $matcher->expects($this->once())
            ->method('match')
            ->with($request)
            ->willReturn($route);
        $this->router->setMatcher($matcher);
        $this->router->handle($request, $response);
    }

    /**
     * Router container mock
     *
     * @return RouterContainer|MockObject
     */
    protected function getRouterContainerMock()
    {
        $class = RouterContainer::class;
        $methods = get_class_methods($class);
        /** @var RouterContainer|MockObject $container */
        $container = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $container;
    }

    /**
     * Get a route matcher mock
     *
     * @return Matcher|MockObject
     */
    protected function getMatcherMock()
    {
        $map = $this->getMockBuilder(Map::Class)
            ->setConstructorArgs([new Route()])
            ->getMock();
        $ruleIterator = $this->getMock(RuleIterator::class);
        $class = Matcher::Class;
        $methods = get_class_methods($class);
        /** @var Matcher|MockObject $matcher */
        $matcher = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->setConstructorArgs(
                [
                    $map,
                    $this->getMock(LoggerInterface::class),
                    $ruleIterator
                ]
            )
            ->getMock();
        return $matcher;
    }

    /**
     * Mocked request object
     *
     * @return MockObject|RequestInterface
     */
    protected function getRequestMock()
    {
        $class = ServerRequestInterface::class;
        $methods = get_class_methods($class);
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $request;
    }
}
