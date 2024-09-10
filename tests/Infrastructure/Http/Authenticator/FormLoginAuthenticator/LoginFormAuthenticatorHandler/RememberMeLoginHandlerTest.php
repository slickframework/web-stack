<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeHandlerInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler\RememberMeLoginHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;

class RememberMeLoginHandlerTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $rememberMeHandler = $this->prophesize(RememberMeHandlerInterface::class);
        $handler = new RememberMeLoginHandler(new FormLoginProperties([]), $rememberMeHandler->reveal());
        $this->assertInstanceOf(RememberMeLoginHandler::class, $handler);
    }

    #[Test]
    public function createsCookieOnAuthenticationSuccess(): void
    {
        $rememberMeHandler = $this->prophesize(RememberMeHandlerInterface::class);
        $handler = new RememberMeLoginHandler(new FormLoginProperties(['rememberMe' => true]), $rememberMeHandler->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getParsedBody()->willReturn(['_rememberMe' => "1"]);
        $token = $this->prophesize(TokenInterface::class);
        $user = $this->prophesize(UserInterface::class);
        $token->user()->willReturn($user->reveal());
        $this->assertNull($handler->onAuthenticationSuccess($request->reveal(), $token->reveal()));
        $rememberMeHandler->createRememberMeCookie($user->reveal())->shouldHaveBeenCalled();
    }

    #[Test]
    public function createsCookieOnlyWhenEnable(): void
    {
        $rememberMeHandler = $this->prophesize(RememberMeHandlerInterface::class);
        $handler = new RememberMeLoginHandler(new FormLoginProperties(['rememberMe' => false]), $rememberMeHandler->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $token = $this->prophesize(TokenInterface::class);
        $user = $this->prophesize(UserInterface::class);
        $token->user()->willReturn($user->reveal());
        $this->assertNull($handler->onAuthenticationSuccess($request->reveal(), $token->reveal()));
        $rememberMeHandler->createRememberMeCookie($user->reveal())->shouldNotHaveBeenCalled();
    }

    #[Test]
    public function onAuthenticationFailure(): void
    {
        $rememberMeHandler = $this->prophesize(RememberMeHandlerInterface::class);
        $handler = new RememberMeLoginHandler(new FormLoginProperties([]), $rememberMeHandler->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $this->assertNull($handler->onAuthenticationFailure($request->reveal(), new AuthenticationException('Test message.')));
    }

    #[Test]
    public function onAuthenticate(): void
    {
        $rememberMeHandler = $this->prophesize(RememberMeHandlerInterface::class);
        $handler = new RememberMeLoginHandler(new FormLoginProperties([]), $rememberMeHandler->reveal());
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $passport = $this->prophesize(PassportInterface::class)->reveal();
        $this->assertSame($passport, $handler->onAuthenticate($request, $passport));
    }
}
