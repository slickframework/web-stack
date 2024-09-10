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
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slick\Http\Message\Uri;

class FormLoginAuthenticatorTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function isInitializable(): void
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler,
        );
        $this->assertInstanceOf(FormLoginAuthenticator::class, $authenticator);
    }

    #[Test]
    public function checkSupports()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $loginPath = '/login';
        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler,
            new FormLoginAuthenticator\FormLoginProperties([
                'paths' => ['check' => $loginPath],
                'parameters' => ['username' => 'email', 'password' => 'password'],
            ])
        );
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getParsedBody()->willReturn(['email' => 'johndoe@example.com', 'password' => 'password']);
        $request->getMethod()->willReturn('POST');
        $request->getUri()->willReturn(new Uri("http://example.com{$loginPath}"));
        $this->assertTrue($authenticator->supports($request->reveal()));
    }

    #[Test]
    public function checkSupportBadMethod()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $loginPath = '/login';
        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler,
            new FormLoginAuthenticator\FormLoginProperties([
                'paths' => ['check' => $loginPath],
            ])
        );
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getParsedBody()->willReturn(['email' => 'johndoe@example.com', 'password' => 'password']);
        $request->getMethod()->willReturn('GET');
        $request->getUri()->willReturn(new Uri("http://example.com{$loginPath}"));
        $this->assertTrue($authenticator->supports($request->reveal()));
    }

    #[Test]
    public function checkSupportWrongPath()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $loginPath = '/sign-in';
        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler,
            new FormLoginAuthenticator\FormLoginProperties([
                'paths' => ['check' => $loginPath],
                'parameters' => ['username' => 'email', 'password' => 'password'],
            ])
        );
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getParsedBody()->willReturn(['email' => 'johndoe@example.com', 'password' => 'password']);
        $request->getMethod()->willReturn('POST');
        $request->getUri()->willReturn(new Uri("http://example.com/login{$loginPath}"));
        $this->assertFalse($authenticator->supports($request->reveal()));
    }

    #[Test]
    public function checkSupportWrongPayload()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $loginPath = '/login';
        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler,
            new FormLoginAuthenticator\FormLoginProperties([
                'paths' => ['check' => $loginPath],
                'parameters' => ['username' => 'email', 'password' => 'password'],
            ])
        );
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getParsedBody()->willReturn(['username' => 'johndoe@example.com', 'password' => 'password']);
        $request->getMethod()->willReturn('POST');
        $request->getUri()->willReturn(new Uri("http://example.com{$loginPath}"));
        $this->assertTrue($authenticator->supports($request->reveal()));
    }



    #[Test]
    public function authenticate(): void
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $userEmail = 'johndoe@example.com';
        $request->getParsedBody()->willReturn(['email' => $userEmail, 'password' => 'password']);


        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class);
        $formLoginHandler->onAuthenticate($request->reveal(), Argument::type(PassportInterface::class))
            ->shouldBeCalled()
            ->willReturnArgument(1);

        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler->reveal(),
            new FormLoginAuthenticator\FormLoginProperties([
                'rememberMe' => false,
                'parameters' => ['username' => 'email', 'password' => 'password'],
            ])
        );

        $passport = $authenticator->authenticate($request->reveal());
        $this->assertEquals($userEmail, $passport->badges()[UserBadge::class]->userIdentifier());
    }

    #[Test]
    public function authenticateBadPostPayload(): void
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler,
            new FormLoginAuthenticator\FormLoginProperties([
                'rememberMe' => false,
                'parameters' => ['username' => 'email', 'password' => 'password'],
            ])
        );
        $request = $this->prophesize(ServerRequestInterface::class);
        $userEmail = 'johndoe@example.com';
        $request->getParsedBody()->willReturn((object) ['email' => $userEmail, 'password' => 'password']);
        $request->getMethod()->willReturn('POST');
        $this->expectException(AuthenticationException::class);
        $passport = $authenticator->authenticate($request->reveal());
        $this->assertNull($passport);
    }

    #[Test]
    public function createToken(): void
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler,
        );
        $passport = $this->prophesize(PassportInterface::class);
        $user = $this->prophesize(UserInterface::class);
        $user->roles()->willReturn(['ROLE_USER']);
        $passport->user()->willReturn($user->reveal());
        $token = $authenticator->createToken($passport->reveal());
        $this->assertSame($user->reveal(), $token->user());
    }

    #[Test]
    public function handleAuthenticationSuccess(): void
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class);
        $token = $this->prophesize(TokenInterface::class)->reveal();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $response = $this->prophesize(ResponseInterface::class)->reveal();

        $formLoginHandler->onAuthenticationSuccess($request, $token)->willReturn($response);

        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler->reveal(),
        );

        $this->assertSame($response, $authenticator->onAuthenticationSuccess($request, $token));
    }

    #[Test]
    public function handleAuthenticationFailureNotPost(): void
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class);

        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler->reveal(),
        );

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('GET');

        $this->assertNull($authenticator->onAuthenticationFailure($request->reveal(), new AuthenticationException('Test')));
    }

    #[Test]
    public function handleAuthenticationFailureRedirect(): void
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class);
        $exception = new AuthenticationException('Test');
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getMethod()->willReturn('POST');
        $userEmail = 'johndoe@example.com';
        $request->getParsedBody()->willReturn(['email' => $userEmail, 'password' => 'password']);

        $formLoginHandler->onAuthenticationFailure($request->reveal(), $exception)->shouldBeCalled()->willReturn(null);
        $logger = $this->prophesize(LoggerInterface::class);


        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler->reveal(),
            new FormLoginAuthenticator\FormLoginProperties([
                'rememberMe' => false,
                'parameters' => ['username' => 'email', 'password' => 'password'],
            ]),
            $logger->reveal(),
        );

        $this->assertNull($authenticator->onAuthenticationFailure($request->reveal(), $exception));

        $logger->info('Authentication failed for user.', ['username' => $userEmail, 'exception' => $exception])->shouldHaveBeenCalled();
    }

    #[Test]
    public function setFormLoginHandler(): void
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $formLoginHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $authenticator = new FormLoginAuthenticator(
            $userProvider->reveal(),
            $formLoginHandler,
        );

        $other = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $this->assertSame($authenticator, $authenticator->withHandler($other));
    }
}
