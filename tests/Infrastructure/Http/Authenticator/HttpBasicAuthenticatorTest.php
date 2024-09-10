<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials\PasswordCredentials;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\HttpBasicAuthenticator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class HttpBasicAuthenticatorTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $realm = "Secured area";
        $authenticator = new HttpBasicAuthenticator($realm, $userProvider->reveal(), $logger->reveal());
        $this->assertInstanceOf(HttpBasicAuthenticator::class, $authenticator);
    }

    #[Test]
    public function checkSupport()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $realm = "Secured area";
        $authenticator = new HttpBasicAuthenticator($realm, $userProvider->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getServerParams()->willReturn(['PHP_AUTH_USER' => 'user'], []);
        $request = $request->reveal();
        $this->assertTrue($authenticator->supports($request));
        $this->assertFalse($authenticator->supports($request));
    }

    #[Test]
    public function entryPoint()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $realm = "Secured area";
        $authenticator = new HttpBasicAuthenticator($realm, $userProvider->reveal());

        $request = $this->prophesize(ServerRequestInterface::class);

        $response = $authenticator->start($request->reveal());
        $this->assertEquals($response->getStatusCode(), 401);
        $this->assertEquals($response->getHeaderLine('WWW-Authenticate'), "Basic realm=\"$realm\"");
    }

    #[Test]
    public function authenticate()
    {
        $user = $this->prophesize(UserInterface::class);
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $loadedUser = $user->reveal();
        $userProvider->loadUserByIdentifier('username')->willReturn($loadedUser);
        $realm = "Secured area";
        $authenticator = new HttpBasicAuthenticator($realm, $userProvider->reveal());

        $password = "password";
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getServerParams()->willReturn(['PHP_AUTH_USER' => 'username', 'PHP_AUTH_PW' => $password], []);

        $passport = $authenticator->authenticate($request->reveal());
        $this->assertInstanceOf(Passport::class, $passport);
        $this->assertSame($passport->user(), $loadedUser);
        $this->assertEquals($passport->badge(PasswordCredentials::class)->password(), $password);
    }

    #[Test]
    public function createToken()
    {
        $user = $this->prophesize(UserInterface::class);
        $roles = ['ROLE_USER'];
        $user->roles()->willReturn($roles);
        $loadedUser = $user->reveal();

        $userProvider = $this->prophesize(UserProviderInterface::class);
        $userProvider->loadUserByIdentifier('username')->willReturn($loadedUser);

        $realm = "Secured area";
        $authenticator = new HttpBasicAuthenticator($realm, $userProvider->reveal());

        $passport = $this->prophesize(Passport::class);
        $passport->user()->willReturn($loadedUser);
        $token = $authenticator->createToken($passport->reveal());
        $this->assertInstanceOf(TokenInterface::class, $token);
        $this->assertSame($loadedUser, $token->user());
        $this->assertEquals($roles, $token->roleNames());
    }

    #[Test]
    public function handleAuthSuccess()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $realm = "Secured area";
        $authenticator = new HttpBasicAuthenticator($realm, $userProvider->reveal(), $logger->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $token = $this->prophesize(TokenInterface::class);

        $this->assertNull($authenticator->onAuthenticationSuccess($request->reveal(), $token->reveal()));
    }

    #[Test]
    public function handleAuthFailure()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $realm = "Secured area";
        $logger = $this->prophesize(LoggerInterface::class);
        $exception = new AuthenticationException('Test');
        $username = 'username';
        $logger->info(
            'Basic authentication failed for user.',
            [
                'username' => $username,
                'exception' => $exception
            ]
        )->shouldBeCalled();

        $authenticator = new HttpBasicAuthenticator($realm, $userProvider->reveal(), $logger->reveal());

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getServerParams()->willReturn(['PHP_AUTH_USER' => $username, 'PHP_AUTH_PW' => 'password']);

        $response = $authenticator->onAuthenticationFailure($request->reveal(), $exception);
        $this->assertEquals($response->getStatusCode(), 401);
        $this->assertEquals($response->getHeaderLine('WWW-Authenticate'), "Basic realm=\"$realm\"");
    }
}
