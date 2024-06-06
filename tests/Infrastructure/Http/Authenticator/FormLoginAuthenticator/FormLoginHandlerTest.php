<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class FormLoginHandlerTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $handler = new FormLoginHandler();
        $this->assertInstanceOf(FormLoginHandler::class, $handler);
    }

    #[Test]
    public function callEveryHandlerOnAuthenticationSuccess(): void
    {
        $handler = $this->prophesize(AuthenticatorHandlerInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $token = $this->prophesize(TokenInterface::class)->reveal();
        $handler->onAuthenticationSuccess($request, $token)->willReturn(null)->shouldBeCalled();
        $handlerList = new FormLoginHandler([$handler->reveal()]);
        $this->assertNull($handlerList->onAuthenticationSuccess($request, $token));
    }

    #[Test]
    public function callEveryHandlerOnAuthenticationSuccessWithResponse(): void
    {
        $handler = $this->prophesize(AuthenticatorHandlerInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $token = $this->prophesize(TokenInterface::class)->reveal();
        $handler->onAuthenticationSuccess($request, $token)->willReturn($response)->shouldBeCalled();
        $handlerList = new FormLoginHandler([$handler->reveal()]);
        $this->assertSame($response, $handlerList->onAuthenticationSuccess($request, $token));
    }

    #[Test]
    public function callEveryHandlerOnAuthenticationFailure(): void
    {
        $handler = $this->prophesize(AuthenticatorHandlerInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $exception = new AuthenticationException('Test');
        $handler->onAuthenticationFailure($request, $exception)->shouldBeCalled()->willReturn(null);
        $handlerList = new FormLoginHandler([$handler->reveal()]);
        $this->assertNull($handlerList->onAuthenticationFailure($request, $exception));
    }

    #[Test]
    public function callEveryHandlerOnAuthenticationFailureWithResponse(): void
    {
        $handler = $this->prophesize(AuthenticatorHandlerInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();
        $exception = new AuthenticationException('Test');
        $handler->onAuthenticationFailure($request, $exception)->shouldBeCalled()->willReturn($response);
        $handlerList = new FormLoginHandler([$handler->reveal()]);
        $this->assertSame($response, $handlerList->onAuthenticationFailure($request, $exception));
    }

    #[Test]
    public function callEveryHandlerOnAuthenticate(): void
    {
        $handler = $this->prophesize(AuthenticatorHandlerInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $passport = $this->prophesize(PassportInterface::class)->reveal();
        $handler->onAuthenticate($request, $passport)->shouldBeCalled()->willReturn($passport);
        $handlerList = new FormLoginHandler([$handler->reveal()]);
        $this->assertSame($passport, $handlerList->onAuthenticate($request, $passport));
    }
}
