<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Router\Builder;

use Aura\Router\Map;
use Aura\Router\Route;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Mvc\Router\Builder\RouteFactory;

/**
 * Route Factory Test Case
 *
 * @package Slick\Tests\Mvc\Router\Builder
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RouteFactoryTest extends TestCase
{

    /**
     * @var RouteFactory
     */
    protected $factory;

    /**
     * Sets the SUT object instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->factory = new RouteFactory();
    }

    /**
     * Clear SUT for next test
     */
    protected function tearDown()
    {
        $this->factory = null;
        parent::tearDown();
    }

    /**
     * Should create a GET route with string as path
     * @test
     */
    public function createRouteSimpleData()
    {
        $route = $this->factory
            ->parse('blog.read', '/blog/{id}', $this->getMapStub());
        $this->assertEquals('/blog/{id}', $route->path);
        $this->assertEquals('blog.read', $route->name);
    }

    /**
     * Provides data for createVerbRoutes test
     *
     * @return array
     */
    public function routeMethodData()
    {
        return [
            'GET' => [
                'blog.read',
                ['path' => '/blog/{id}'],
                ['GET']
            ],
            'POST' => [
                'blog.add',
                ['method' => 'POST', 'path' => '/blog/add'],
                ['POST']
            ],
            'PUT' => [
                'blog.edit',
                ['method' => 'PUT','allows' => ['POST'], 'path' => '/blog/{id}/edit'],
                ['POST', 'PUT']
            ],
            'DELETE' => [
                'blog.delete',
                ['allows' => ['DELETE'], 'path' => '/blog/{id}/delete'],
                ['DELETE', 'GET']
            ],
        ];
    }

    /**
     * Should create a route for the specific method name
     *
     * @param string $name
     * @param array  $data
     * @param string $method
     *
     * @test
     * @dataProvider routeMethodData
     */
    public function createVerbRoutes($name, array $data, $method)
    {
        $route = $this->factory
            ->parse($name, $data, $this->getMapStub());
        $this->assertEquals($method, $route->allows);
    }

    /**
     * Should run the method name equals to the key in data and
     * pass the data value as argument.
     * @test
     */
    public function addRouteProperties()
    {
        $data = [
            'defaults' => [
                'id' => null
            ],
            'path' => '/blog/{id}/edit',
            'allows' => ['GET', 'POST']
        ];
        $route = $this->factory
            ->parse('blog.edit', $data, $this->getMapStub());
        $this->assertEquals([
            'id' => null
        ], $route->defaults);
    }


    /**
     * @return Map|MockObject
     */
    public function getMapStub()
    {
        $route = new Route();

        $class = Map::class;
        $methods = get_class_methods($class);
        /** @var Map|MockObject $map */
        $map = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->setConstructorArgs([new Route()])
            ->getMock();
        $map->method('get')->willReturnCallback(
            function($name, $path) use (&$route) {
                $route->allows('GET');
                $route->name($name);
                $route->path($path);
                return $route;
            }
        );
        $map->method('route')->willReturn($route);
        return $map;
    }

}
