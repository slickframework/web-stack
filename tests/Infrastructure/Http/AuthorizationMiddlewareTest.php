<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http;

use Slick\WebStack\Domain\Security\AuthorizationCheckerInterface;
use Slick\WebStack\Infrastructure\Http\AuthorizationMiddleware;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizationMiddlewareTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function initialize(): void
    {
        $checker = $this->prophesize(AuthorizationCheckerInterface::class)->reveal();
        $middleware = new AuthorizationMiddleware($checker);
        $this->assertInstanceOf(AuthorizationMiddleware::class, $middleware);
    }

    #[Test]
    public function processPass(): void
    {
        $checker = $this->prophesize(AuthorizationCheckerInterface::class);
        $checker->isGranted("ROLE_ADMIN")->willReturn(true);
        $checker->isGranted("ROLE_USER")->willReturn(false);
        $middleware = new AuthorizationMiddleware($checker->reveal());

        $parameters = [
            "_route" => "homepage",
            "_controller" => DummyController::class,
            "_action" => "handle"
        ];
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute("route")->willReturn($parameters);
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $handle = $this->prophesize(RequestHandlerInterface::class);
        $handle->handle($request->reveal())->willReturn($response);

        $resp = $middleware->process($request->reveal(), $handle->reveal());
        $this->assertSame($resp, $response);
        $checker->isGranted("ROLE_USER")->shouldHaveBeenCalled();
    }

    #[Test]
    public function processFail(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class);

        $checker = $this->prophesize(AuthorizationCheckerInterface::class);
        $checker->isGranted("ROLE_ADMIN")->willReturn(false);
        $checker->isGranted("ROLE_USER")->willReturn(false);
        $checker->processEntryPoint($request)->willReturn(null);
        $middleware = new AuthorizationMiddleware($checker->reveal());

        $parameters = [
            "_route" => "homepage",
            "_controller" => DummyController::class,
            "_action" => "handle"
        ];
        $request->getAttribute("route")->willReturn($parameters);
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $handle = $this->prophesize(RequestHandlerInterface::class);
        $handle->handle($request->reveal())->willReturn($response);

        $resp = $middleware->process($request->reveal(), $handle->reveal());
        $this->assertNotSame($resp, $response);
        $checker->isGranted("ROLE_USER")->shouldHaveBeenCalled();
        $checker->isGranted("ROLE_ADMIN")->shouldHaveBeenCalled();
        $this->assertEquals(403, $resp->getStatusCode());
    }

    #[Test]
    public function processNoAttribute(): void
    {
        $checker = $this->prophesize(AuthorizationCheckerInterface::class);
        $middleware = new AuthorizationMiddleware($checker->reveal());

        $parameters = [
            "_route" => "homepage",
            "_controller" => NoGrantController::class,
            "_action" => "handle"
        ];
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute("route")->willReturn($parameters);
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $handle = $this->prophesize(RequestHandlerInterface::class);
        $handle->handle($request->reveal())->willReturn($response);

        $resp = $middleware->process($request->reveal(), $handle->reveal());
        $this->assertSame($resp, $response);
    }

    #[Test]
    public function processNoRoute(): void
    {
        $checker = $this->prophesize(AuthorizationCheckerInterface::class);
        $middleware = new AuthorizationMiddleware($checker->reveal());

        $parameters = [
            "_route" => "homepage",
            "_action" => "handle"
        ];
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute("route")->willReturn($parameters);
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $handle = $this->prophesize(RequestHandlerInterface::class);
        $handle->handle($request->reveal())->willReturn($response);

        $resp = $middleware->process($request->reveal(), $handle->reveal());
        $this->assertSame($resp, $response);
    }
}
