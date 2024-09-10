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
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler\RedirectHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Uri;
use Slick\Http\Session\SessionDriverInterface;

class RedirectHandlerTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class)->reveal();
        $handler = new RedirectHandler($session);
        $this->assertInstanceOf(RedirectHandler::class, $handler);
    }

    #[Test]
    public function onAuthenticateDoNothing(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class)->reveal();
        $handler = new RedirectHandler($session);
        $passport = $this->prophesize(PassportInterface::class)->reveal();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();

        $this->assertSame($passport, $handler->onAuthenticate($request, $passport));
    }

    #[Test]
    public function onAuthenticationFailureWithRedirect(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class)->reveal();
        $handler = new RedirectHandler($session);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/some-path'));
        $response = $handler->onAuthenticationFailure($request->reveal(), new AuthenticationException('Test'));

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }

    #[Test]
    public function onAuthenticationFailureNull(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class)->reveal();
        $handler = new RedirectHandler($session);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/login'));
        $response = $handler->onAuthenticationFailure($request->reveal(), new AuthenticationException('Test'));

        $this->assertNull($response);
    }

    #[Test]
    public function successRedirectDefault(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class);
        $session->get(RedirectHandler::LAST_URI)->willReturn(null);
        $handler = new RedirectHandler($session->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('referer')->willReturn('');
        $token = $this->prophesize(TokenInterface::class)->reveal();

        $response = $handler->onAuthenticationSuccess($request->reveal(), $token);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/', $response->getHeaderLine('Location'));
    }

    #[Test]
    public function successRedirectSession(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class);
        $session->get(RedirectHandler::LAST_URI)->willReturn('/some-path');
        $handler = new RedirectHandler($session->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('referer')->willReturn('');
        $token = $this->prophesize(TokenInterface::class)->reveal();

        $response = $handler->onAuthenticationSuccess($request->reveal(), $token);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('/some-path', $response->getHeaderLine('Location'));
    }

    #[Test]
    public function successRedirectReferer(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class);
        $session->get(RedirectHandler::LAST_URI)->willReturn('/some-path');
        $handler = new RedirectHandler($session->reveal(), new FormLoginProperties(['useReferer' => true]));
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getHeaderLine('referer')->willReturn('/other-path');
        $token = $this->prophesize(TokenInterface::class)->reveal();

        $response = $handler->onAuthenticationSuccess($request->reveal(), $token);
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals('/other-path', $response->getHeaderLine('Location'));
    }
}
