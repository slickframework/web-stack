<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\AuthenticationEntryPoint;

use Slick\WebStack\Infrastructure\Http\AuthenticationEntryPoint\FormLoginEntryPoint;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler\RedirectHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Uri;
use Slick\Http\Session\SessionDriverInterface;

class FormLoginEntryPointTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class);
        $FormLoginEntryPoint = new FormLoginEntryPoint($session->reveal());
        $this->assertInstanceOf(FormLoginEntryPoint::class, $FormLoginEntryPoint);
    }

    #[Test]
    public function savesUrlAndRedirectsToLogin(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/home'));
        $session->set(RedirectHandler::LAST_URI, "/home")->shouldBeCalled()->willReturn($session->reveal());
        $FormLoginEntryPoint = new FormLoginEntryPoint($session->reveal());
        $response = $FormLoginEntryPoint->start($request->reveal());
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('/login', $response->getHeaderLine('Location'));
    }
}
