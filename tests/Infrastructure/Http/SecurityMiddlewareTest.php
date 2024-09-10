<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http;

use Slick\WebStack\Domain\Security\AuthorizationCheckerInterface;
use Slick\WebStack\Domain\Security\SecurityAuthenticatorInterface;
use Slick\WebStack\Infrastructure\Http\SecurityMiddleware;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SecurityMiddlewareTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $security = $this->prophesize(SecurityAuthenticatorInterface::class);
        $auth = $this->prophesize(AuthorizationCheckerInterface::class)->reveal();
        $middleware = new SecurityMiddleware($security->reveal(), $auth);
        $this->assertInstanceOf(SecurityMiddleware::class, $middleware);
        $this->assertInstanceOf(MiddlewareInterface::class, $middleware);
    }

    #[Test]
    public function processResponse(): void
    {
        $security = $this->prophesize(SecurityAuthenticatorInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $handler = $this->prophesize(RequestHandlerInterface::class)->reveal();
        $security->process($request)->willReturn($response);
        $auth = $this->prophesize(AuthorizationCheckerInterface::class);
        $auth->isGrantedAcl($request)->willReturn(true);

        $middleware = new SecurityMiddleware($security->reveal(), $auth->reveal());

        $this->assertSame($response, $middleware->process($request, $handler));

    }

    #[Test]
    public function processNoResponse(): void
    {
        $security = $this->prophesize(SecurityAuthenticatorInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request)->willReturn($response);
        $security->process($request)->willReturn(null);
        $auth = $this->prophesize(AuthorizationCheckerInterface::class);
        $auth->isGrantedAcl($request)->willReturn(true);

        $middleware = new SecurityMiddleware($security->reveal(), $auth->reveal());

        $this->assertSame($response, $middleware->process($request, $handler->reveal()));

    }

    #[Test]
    public function processFailAcl(): void
    {
        $security = $this->prophesize(SecurityAuthenticatorInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $handler = $this->prophesize(RequestHandlerInterface::class);
        $handler->handle($request)->willReturn($response);
        $security->process($request)->willReturn(null);
        $auth = $this->prophesize(AuthorizationCheckerInterface::class);
        $auth->isGrantedAcl($request)->willReturn(false);

        $middleware = new SecurityMiddleware($security->reveal(), $auth->reveal());

        $this->assertNotSame($response, $middleware->process($request, $handler->reveal()));
        $this->assertEquals(403, $middleware->process($request, $handler->reveal())->getStatusCode());
    }
}
