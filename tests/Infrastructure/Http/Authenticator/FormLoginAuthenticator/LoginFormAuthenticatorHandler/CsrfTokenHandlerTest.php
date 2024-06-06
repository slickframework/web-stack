<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Csrf\CsrfTokenManagerInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\CsrfBadge;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler\CsrfTokenHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;

class CsrfTokenHandlerTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $tokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $props = new FormLoginProperties(['enableCsrf' => true]);
        $handler = new CsrfTokenHandler($props, $tokenManager->reveal());
        $this->assertInstanceOf(CsrfTokenHandler::class, $handler);
    }

    #[Test]
    public function addsCsrfBadgeWhenEnabled()
    {
        $tokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $props = new FormLoginProperties(['enableCsrf' => true]);
        $handler = new CsrfTokenHandler($props, $tokenManager->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getParsedBody()->willReturn(['_csrf' => 'some-token-value']);
        $passport = $this->prophesize(PassportInterface::class);
        $passport->addBadge(Argument::type(CsrfBadge::class))->shouldBeCalled()->willReturn($passport);
        $this->assertSame($passport->reveal(), $handler->onAuthenticate($request->reveal(), $passport->reveal()));
    }

    #[Test]
    public function doNotAddCsrfBadgeWhenNotEnabled()
    {
        $tokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $props = new FormLoginProperties(['enableCsrf' => false]);
        $handler = new CsrfTokenHandler($props, $tokenManager->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $passport = $this->prophesize(PassportInterface::class);
        $passport->addBadge(Argument::type(CsrfBadge::class))->shouldNotBeCalled()->willReturn($passport);
        $this->assertSame($passport->reveal(), $handler->onAuthenticate($request->reveal(), $passport->reveal()));
    }

    #[Test]
    public function onAuthenticationSuccess(): void
    {
        $tokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $props = new FormLoginProperties(['enableCsrf' => true]);
        $handler = new CsrfTokenHandler($props, $tokenManager->reveal());
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $token = $this->prophesize(TokenInterface::class)->reveal();
        $this->assertNull($handler->onAuthenticationSuccess($request, $token));
    }

    #[Test]
    public function onAuthenticationFailure(): void
    {
        $tokenManager = $this->prophesize(CsrfTokenManagerInterface::class);
        $props = new FormLoginProperties(['enableCsrf' => true]);
        $handler = new CsrfTokenHandler($props, $tokenManager->reveal());
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $exception = new AuthenticationException('Test');
        $this->assertNull($handler->onAuthenticationFailure($request, $exception));
    }
}
