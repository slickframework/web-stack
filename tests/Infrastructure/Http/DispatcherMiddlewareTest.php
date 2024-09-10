<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http;

use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slick\Di\ContainerInterface;
use Slick\WebStack\Infrastructure\Http\DispatcherMiddleware;
use PHPUnit\Framework\TestCase;
use Test\Slick\WebStack\Infrastructure\Http\Dispatcher\SimpleController;
use Test\Slick\WebStack\Infrastructure\Http\Dispatcher\SomeInterface;

class DispatcherMiddlewareTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $middleware = new DispatcherMiddleware($container->reveal());
        $this->assertInstanceOf(DispatcherMiddleware::class, $middleware);
    }

    #[Test]
    public function callHandlerWithoutArguments(): void
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->make(SimpleController::class)->willReturn(new SimpleController());
        $middleware = new DispatcherMiddleware($container->reveal());
        $response = $middleware->process($this->prepareRequest()->reveal(), $handler->reveal());
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }

    #[Test]
    public function noResponseHandle()
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->make(SimpleController::class)->willReturn(new SimpleController());
        $middleware = new DispatcherMiddleware($container->reveal());
        $response = $this->prophesize(ResponseInterface::class);
        $request = $this->prepareRequest(action: "noResponse")->reveal();
        $handler->handle($request)->willReturn($response->reveal());
        $result = $middleware->process($request, $handler->reveal());
        $this->assertSame($response->reveal(), $result);
    }

    #[Test]
    public function injectParameters(): void
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->make(SimpleController::class)->willReturn(new SimpleController());
        $middleware = new DispatcherMiddleware($container->reveal());
        $response = $middleware->process(
            $this->prepareRequest(action: 'withRouteParams')->reveal(),
            $handler->reveal()
        );
        $response->getBody()->rewind();
        $content = $response->getBody()->getContents();
        $this->assertEquals($content, "test-param");
    }

    #[Test]
    public function addContainerArgs()
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->make(SimpleController::class)->willReturn(new SimpleController());
        $container->has(SomeInterface::class)->willReturn(true);
        $container->has('?string')->willReturn(false);
        $container->get(SomeInterface::class)->willReturn($this->prophesize(SomeInterface::class)->reveal());
        $middleware = new DispatcherMiddleware($container->reveal());
        $response = $middleware->process(
            $this->prepareRequest(action: 'withContainerParam')->reveal(),
            $handler->reveal()
        );
        $response->getBody()->rewind();
        $content = $response->getBody()->getContents();
        $this->assertEquals($content, "test-param");
    }

    #[Test]
    public function missingArgument(): void
    {
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $container = $this->prophesize(ContainerInterface::class);
        $container->make(SimpleController::class)->willReturn(new SimpleController());
        $container->has('string')->willReturn(false);
        $middleware = new DispatcherMiddleware($container->reveal());
        $this->expectException(\RuntimeException::class);
        $middleware->process($this->prepareRequest(action: 'missing')->reveal(), $handler->reveal());
    }



    private function prepareRequest(
        string $controller = SimpleController::class,
        string $action = 'action'
    ): ServerRequestInterface|ObjectProphecy {
        $request = $this->prophesize(ServerRequestInterface::class);
        $route = [
            '_route' => 'test',
            '_controller' => $controller,
            '_action' => $action,
            'id' => "test-param"
        ];
        $request->getAttribute('route')->willReturn($route);
        return $request;
    }
}
