<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Http;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\PhpEnvironment\Response;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Http\Session\Driver\NullDriver;
use Slick\Http\SessionDriverInterface;
use Slick\Mvc\Application;
use Slick\Mvc\ControllerInterface;
use Slick\Mvc\Http\FlashMessages;
use Slick\Mvc\Http\Session;
use Slick\Mvc\Http\SessionAwareMethods;

/**
 * Session Test
 *
 * @package Slick\Tests\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class SessionTest extends TestCase
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var Request
     */
    protected $request;

    use SessionAwareMethods;

    /**
     * Sets the SUT object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->request = new Request();
        $this->session = new Session();
        $this->session->setNext(new GrabRequest());
        Application::setContainer($this->getContainer());
    }

    /**
     * Should add a session service into 'session' request attribute
     * @test
     */
    public function registersSession()
    {
        /** @var Response $response */
        $response = $this->getMock(Response::class);
        $this->session->handle($this->request, $response);
        $this->assertInstanceOf(
            SessionDriverInterface::class,
            GrabRequest::$request->getAttribute('session')
        );
        return GrabRequest::$request;
    }

    /**
     * @param Request $request
     * @test
     * @depends registersSession
     */
    public function registerFlashMessages(Request $request)
    {
        $key = ControllerInterface::REQUEST_ATTR_VIEW_DATA;
        $var = $request->getAttribute($key)['flashMessages'];
        $this->assertInstanceOf(FlashMessages::class,  $var);
    }

    /**
     * Test trait
     * @test
     */
    public function getSessionObject()
    {
        $this->assertInstanceOf(
            SessionDriverInterface::class,
            $this->getSessionDriver()
        );
    }

    protected function getContainer()
    {
        $class = ContainerInterface::class;
        $methods = get_class_methods($class);
        $container = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        $container->method('get')
            ->with('session')
            ->willReturn(new NullDriver());
        return $container;
    }
}

class GrabRequest extends AbstractMiddleware implements MiddlewareInterface
{

    /**
     * @var Request
     */
    public static $request;

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
        self::$request = $request;
    }
}