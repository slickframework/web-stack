<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc;

use Aura\Router\Route;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\PhpEnvironment\Response;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Mvc\ControllerInterface;
use Slick\Mvc\Dispatcher;

/**
 * Dispatcher Test Case
 *
 * @package Slick\Tests\Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class DispatcherTest extends TestCase
{

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    /**
     * Sets the SUT dispatcher object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->dispatcher = new Dispatcher();
    }

    /**
     * Clear SUT for next test
     */
    protected function tearDown()
    {
        $this->dispatcher = null;
        parent::tearDown();
    }

    /**
     * @test
     */
    public function dispatchHome()
    {
        $route = new Route();
        $request = (new Request())
            ->withAttribute('route', $route);
        $route->attributes(
            [
                'namespace' => 'Slick\Tests\Mvc\Controller',
                'action' => 'index',
                'controller' => 'test'
            ]
        );
        $response = new Response();
        $this->dispatcher->setNext(new TestMiddleWare());
        $this->dispatcher->handle($request, $response);
        $this->assertEquals('Test::index()', TestMiddleWare::$result);
    }

    /**
     * @test
     * @expectedException \Slick\MVC\Exception\ControllerNotFoundException
     */
    public function dispatchNull()
    {
        $route = new Route();
        $request = (new Request())
            ->withAttribute('route', $route);
        $response = new Response();
        $this->dispatcher->handle($request, $response);
    }

    /**
     * @test
     */
    public function dispatchWithArguments()
    {
        $route = new Route();
        $request = (new Request())
            ->withAttribute('route', $route);
        $route->attributes(
            [
                'namespace' => 'Slick\Tests\Mvc\Controller',
                'action' => 'other-method',
                'controller' => 'test',
                'args' => ['test']
            ]
        );
        $response = new Response();
        $this->dispatcher->setNext(new TestMiddleWare());
        $this->dispatcher->handle($request, $response);
        $this->assertEquals('Test::otherMethod(test)', TestMiddleWare::$result);
    }

    /**
     * @test
     * @expectedException \Slick\MVC\Exception\ControllerMethodNotFoundException
     */
    public function methodNotFound()
    {
        $route = new Route();
        $request = (new Request())
            ->withAttribute('route', $route);
        $route->attributes(
            [
                'namespace' => 'Slick\Tests\Mvc\Controller',
                'action' => 'unknown-method',
                'controller' => 'test'
            ]
        );
        $response = new Response();
        $this->dispatcher->handle($request, $response);
    }

    /**
     * @test
     * @expectedException \Slick\Mvc\Exception\InvalidControllerException
     */
    public function invalidController()
    {
        $route = new Route();
        $request = (new Request())
            ->withAttribute('route', $route);
        $route->attributes(
            [
                'namespace' => 'Slick\Tests\Mvc',
                'controller' => 'TestMiddleWare'
            ]
        );
        $response = new Response();
        $this->dispatcher->handle($request, $response);
    }
}


class TestMiddleWare extends AbstractMiddleware implements MiddlewareInterface
{

    public static $result = null;

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
        self::$result = $request->getAttribute(
            ControllerInterface::REQUEST_ATTR_VIEW_DATA
        )['test'];
        return $response;
    }
}
