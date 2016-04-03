<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc;

use Aura\Router\Generator;
use Aura\Router\RouterContainer;
use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Di\Container;
use Slick\Di\ContainerBuilder;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\PhpEnvironment\Response;
use Slick\Mvc\Application;
use Slick\Mvc\Controller;
use Slick\Mvc\ControllerInterface;
use Slick\Mvc\Router;

/**
 * Controller Test case
 *
 * @package Slick\Tests\Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ControllerTest extends TestCase
{
    /**
     * @var Controller|MockObject
     */
    protected $controller;

    /**
     * Sets the SUT controller object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->controller = $this->getMockForAbstractClass(Controller::class);
        $this->controller->register(new Request(), new Response());
    }

    /**
     * Clears for next test
     */
    protected function tearDown()
    {
        $this->controller = null;
        parent::tearDown();
    }

    /**
     * Should return the response object
     * @test
     */
    public function getResponse()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->controller->getResponse()
        );
    }

    /**
     * Should write an attribute in the request object
     * @test
     */
    public function setSingleValue()
    {
        $index = ControllerInterface::REQUEST_ATTR_VIEW_DATA;
        $this->controller->set('foo', 'bar');
        $request = $this->controller->getRequest();
        $this->assertEquals(
            'bar',
            $request->getAttribute($index)['foo']
        );
    }

    /**
     * Should add every value in the viewVars attribute on the request
     * @test
     */
    public function setMultipleValues()
    {
        $foo = 'bar';
        $baz = 'moo';
        $index = ControllerInterface::REQUEST_ATTR_VIEW_DATA;
        $expected = ['foo' => 'bar', 'baz' => 'moo'];
        $this->controller->set(compact('foo', 'baz'));
        $this->assertEquals(
            $expected,
            $this->controller->getRequest()->getAttribute($index)
        );
    }

    /**
     * Should change the default template
     * @test
     */
    public function setView()
    {
        $this->controller->setView('some/test');
        $this->assertEquals(
            'some/test',
            $this->controller->getRequest()->getAttribute('template')
        );
    }

    /**
     * Should set render to false
     * @test
     */
    public function disableRendering()
    {
        $this->controller->disableRendering();
        $this->assertFalse(
            $this->controller->getRequest()->getAttribute('render')
        );
    }

    /**
     * Should not change the passed path
     * @test
     */
    public function redirectUrl()
    {
        $this->controller->redirect('https://google.com');
        $response = $this->controller->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('https://google.com', $response->getHeader('location')[0]);
    }

    /**
     * Should use the route container generator to determine the path
     * @test
     */
    public function generatedPath()
    {
        $generator = $this->getMockBuilder(Generator::class)
            ->disableOriginalConstructor()
            ->setMethods(['generate'])
            ->getMock();
        $generator->expects($this->once())
            ->method('generate')
            ->with('blog.edit', ['id' => 2])
            ->willReturn(false);
        $routerContainer = $this->getMockBuilder(RouterContainer::class)
            ->disableOriginalConstructor()
            ->setMethods(['getGenerator'])
            ->getMock();
        $routerContainer->method('getGenerator')->willReturn($generator);
        $router = $this->getMockBuilder(Router::class)
            ->setMethods(['getRouterContainer'])
            ->getMock();
        $router->method('getRouterContainer')->willReturn($routerContainer);
        $container = (new ContainerBuilder(['router.middleware' => $router], true))
            ->getContainer();
        Application::setContainer($container);
        
        $this->controller->redirect('blog.edit', ['id' => 2]);
        $response = $this->controller->getResponse();
        $this->assertEquals(302, $response->getStatusCode());
        $basePath = rtrim($this->controller->getRequest()->getBasePath(), '/');
        $this->assertEquals($basePath.'/blog.edit', $response->getHeader('location')[0]);
    }
}
