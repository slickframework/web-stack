<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ResponseInterface;
use Slick\Di\Container;
use Slick\Http\PhpEnvironment\MiddlewareRunnerInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\Server;
use Slick\Mvc\Application;

/**
 * Application test case
 *
 * @package Slick\Tests\Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ApplicationTest extends TestCase
{

    /**
     * @var Application
     */
    protected $application;

    /**
     * Sets the SUT application object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->application = new Application();
    }

    /**
     * Should initialize and return a static container interface
     * @test
     */
    public function getContainer()
    {
        $container = Application::container();
        $this->assertInstanceOf(ContainerInterface::class, $container);
        return $container;
    }

    /**
     * Container is stores statically inside application class
     *
     * @param ContainerInterface $container
     * @test
     * @depends getContainer
     */
    public function testStaticAccessToContainer(ContainerInterface $container)
    {
        $this->assertSame($container, $this->application->container());
    }

    /**
     * Should get the request object from the container
     * @test
     */
    public function getRequest()
    {
        $mockedRequest = $this->getMockedRequest();
        $container = $this->getMockedContainer();
        $container->expects($this->once())
            ->method('get')
            ->with('request')
            ->willReturn($mockedRequest);
        Application::setContainer($container);
        $request = $this->application
            ->getRequest();
        $this->assertSame($request, $mockedRequest);
    }

    /**
     * Should get the response from the middleware run method
     * @test
     */
    public function getResponse()
    {
        $container = $this->getMockedContainer();
        $container->expects($this->once())
            ->method('get')
            ->with('middleware.runner')
            ->willReturn($this->getMiddlewareRunnerMock());
        Application::setContainer($container);
        $this->application->setRequest(new Request());
        $response = $this->application
            ->getResponse();
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    /**
     * Get a mocked request
     *
     * @return MockObject|Request
     */
    protected function getMockedRequest()
    {
        $class = Request::class;
        $methods = get_class_methods($class);
        /** @var MockObject|Request $request */
        $request = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
        return $request;
    }

    /**
     * Get mocked container object
     *
     * @return ContainerInterface|MockObject
     */
    protected function getMockedContainer()
    {
        $class = Container::class;
        $methods = get_class_methods($class);
        /** @var ContainerInterface|MockObject $container */
        $container = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $container;
    }

    /**
     * Gets middleware runner mocked
     *
     * @return MockObject|MiddlewareRunnerInterface
     */
    protected function getMiddlewareRunnerMock()
    {
        $class = Server::class;
        /** @var MiddlewareRunnerInterface|MockObject $middleWare */
        $middleWare = $this->getMockBuilder($class)
            ->setMethods(get_class_methods($class))
            ->getMock();
        $middleWare->method('run')
            ->willReturn($this->getMock(ResponseInterface::class));
        $middleWare->method('setRequest')
            ->willReturn($middleWare);
        return $middleWare;
    }
}
