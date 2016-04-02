<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Router;

use Aura\Router\Map;
use Aura\Router\Route;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Mvc\Router\Builder\RouteFactory;
use Slick\Mvc\Router\RouteBuilder;

/**
 * Route Builder Test Case
 *
 * @package Slick\Tests\Mvc\Router
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RouteBuilderTest extends TestCase
{
    /**
     * @var RouteBuilder
     */
    protected $builder;

    /**
     * Sets the SUT object instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->builder = new RouteBuilder(__DIR__.'/routes.yml');
    }

    /**
     * Should parse the YML file
     * @test
     */
    public function loadYmlDefinitions()
    {
        $data = $this->builder->getData();
        $this->assertTrue(is_array($data));
    }

    /**
     * Should throw RoutesFileParseException
     * @test
     * @expectedException \Slick\MVC\Exception\RoutesFileParseException
     */
    public function parseFileWithError()
    {
        $builder = new RouteBuilder(__DIR__.'/bad.yml');
        $builder->getData();
    }

    /**
     * Should throw RoutesFileNotFoundException
     * @test
     * @expectedException \Slick\MVC\Exception\RoutesFileNotFoundException
     */
    public function noFileWithError()
    {
        $builder = new RouteBuilder(__DIR__.'/_unknown_.yml');
        $builder->getData();
    }

    /**
     * Should create a new route factory
     * @test
     */
    public function createRouteFactory()
    {
        $factory = $this->builder->getRouteFactory();
        $this->assertInstanceOf(RouteFactory::class, $factory);
    }

    /**
     * Should take routes from YML definitions file and call the
     * RouteFactory::parse() to inject them into the route map
     * @test
     */
    public function buildRouteMap()
    {
        /** @var Map|MockObject $map */
        $map = $this->getMockBuilder(Map::class)
            ->setConstructorArgs([new Route()])
            ->setMethods(['tokens'])
            ->getMock();
        $map->expects($this->once())
            ->method('tokens')
            ->with(['id' => '\d+']);

        $factory = $this->getFactoryMock();

        $factory->expects($this->atLeast(3))
            ->method('parse');

        $this->builder->setRouteFactory($factory);
        $this->builder->build($map);
    }

    /**
     * Factory mock object
     *
     * @return MockObject|RouteFactory
     */
    protected function getFactoryMock()
    {
        $class = RouteFactory::class;
        $methods = get_class_methods($class);
        /** @var RouteFactory|MockObject $factory */
        $factory = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $factory;
    }

}
