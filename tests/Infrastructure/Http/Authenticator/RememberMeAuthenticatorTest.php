<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator;

use Slick\WebStack\Domain\Security\Authentication\Token\RememberMeToken;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\Exception\UserNotFoundException;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\SelfValidatingPassport;
use Slick\WebStack\Domain\Security\Http\AuthenticatorFactoryInterface;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeDetails;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeHandlerInterface;
use Slick\WebStack\Domain\Security\Signature\SignatureHasher;
use Slick\WebStack\Infrastructure\Http\Authenticator\RememberMeAuthenticator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slick\Di\ContainerInterface;
use Slick\Di\Exception\NotFoundException;
use Test\Slick\WebStack\Domain\Security\Signature\DummyUser;

class RememberMeAuthenticatorTest extends TestCase
{
    use ProphecyTrait;


    #[Test]
    public function initializable(): void
    {
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $secret = 'SomeSecret';
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $authenticator = new RememberMeAuthenticator($rmHandler, $secret, $tokenStorage);
        $this->assertInstanceOf(RememberMeAuthenticator::class, $authenticator);
    }
    
    #[Test]
    public function supportsWhenCookieIsPresent()
    {
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $secret = 'SomeSecret';
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->debug(Argument::any())->shouldBeCalled();
        $authenticator = new RememberMeAuthenticator($rmHandler, $secret, $tokenStorage, logger: $logger->reveal());

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getCookieParams()->willReturn([RememberMeAuthenticator::COOKIE_NAME => 'foo']);
        $this->assertTrue($authenticator->supports($request->reveal()));
    }

    #[Test]
    public function notSupportsWhenCookieIsNotPresent()
    {
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $secret = 'SomeSecret';
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->debug(Argument::any())->shouldNotBeCalled();
        $authenticator = new RememberMeAuthenticator($rmHandler, $secret, $tokenStorage, logger: $logger->reveal());

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getCookieParams()->willReturn([]);
        $this->assertFalse($authenticator->supports($request->reveal()));
    }

    #[Test]
    public function doNothingOnSuccess()
    {
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $secret = 'SomeSecret';
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $authenticator = new RememberMeAuthenticator($rmHandler, $secret, $tokenStorage);
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $token = $this->prophesize(TokenInterface::class)->reveal();
        $this->assertNull($authenticator->onAuthenticationSuccess($request, $token));
    }

    #[Test]
    public function authenticate(): void
    {
        $details = new RememberMeDetails(DummyUser::class, 'userIdentifier', time() + 60*60, 'test');
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $secret = 'SomeSecret';
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $authenticator = new RememberMeAuthenticator($rmHandler, $secret, $tokenStorage);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getCookieParams()->willReturn([RememberMeAuthenticator::COOKIE_NAME => (string) $details]);
        $passport = $authenticator->authenticate($request->reveal());
        $this->assertInstanceOf(SelfValidatingPassport::class, $passport);
    }

    #[Test]
    public function createToken(): void
    {
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $secret = 'SomeSecret';
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $authenticator = new RememberMeAuthenticator($rmHandler, $secret, $tokenStorage);
        $user = new DummyUser();
        $passport = $this->prophesize(PassportInterface::class);
        $passport->user()->willReturn($user);
        $token = $authenticator->createToken($passport->reveal());
        $this->assertInstanceOf(RememberMeToken::class, $token);
    }

    #[Test]
    public function skipSupportWhenAlreadyAuthenticated(): void
    {
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $secret = 'SomeSecret';
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $authenticator = new RememberMeAuthenticator($rmHandler, $secret, $tokenStorage->reveal());
        $token = $this->prophesize(TokenInterface::class);
        $tokenStorage->getToken()->willReturn($token->reveal());
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $this->assertFalse($authenticator->supports($request));
    }

    #[Test]
    public function onUserNotFoundFailure(): void
    {
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $secret = 'SomeSecret';
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $logger = $this->prophesize(LoggerInterface::class);
        $exception = new UserNotFoundException();
        $logger->info(
            'User for remember-me cookie not found.',
            ['exception' => $exception]
        )->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $authenticator = new RememberMeAuthenticator($rmHandler, $secret, $tokenStorage, logger: $logger->reveal());
        $this->assertNull($authenticator->onAuthenticationFailure($request, $exception));
    }

    #[Test]
    public function onAuthenticationFailure(): void
    {
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $secret = 'SomeSecret';
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $logger = $this->prophesize(LoggerInterface::class);
        $exception = new AuthenticationException();
        $logger->debug(
            'Remember me authentication failed.',
            ['exception' => $exception]
        )->shouldBeCalled();

        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $authenticator = new RememberMeAuthenticator($rmHandler, $secret, $tokenStorage, logger: $logger->reveal());
        $this->assertNull($authenticator->onAuthenticationFailure($request, $exception));
    }


}
