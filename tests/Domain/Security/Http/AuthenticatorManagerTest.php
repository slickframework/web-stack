<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Exception\BadCredentialsException;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials\PasswordCredentials;
use Slick\WebStack\Domain\Security\Http\AuthenticatorInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorManager;
use Slick\WebStack\Domain\Security\Http\AuthenticatorManagerInterface;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use Slick\WebStack\Domain\Security\User\PasswordAuthenticatedUserInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class AuthenticatorManagerTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $hasher = $this->prophesize(PasswordHasherInterface::class)->reveal();
        $authenticator = $this->prophesize(AuthenticatorInterface::class);
        $tokenStorage  = $this->prophesize(TokenStorageInterface::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher, $logger->reveal());
        $this->assertInstanceOf(AuthenticatorManagerInterface::class, $authenticatorManager);
        $this->assertInstanceOf(AuthenticatorManager::class, $authenticatorManager);
    }

    #[Test]
    public function supports()
    {
        $hasher = $this->prophesize(PasswordHasherInterface::class)->reveal();
        $request = $this->prophesize(ServerRequestInterface::class);
        $authenticator = $this->prophesize(AuthenticatorInterface::class);

        $request->withAttribute(AuthenticatorManager::AUTHENTICATORS_ATTRIBUTE_KEY, [$authenticator])
            ->willReturn($request)
            ->shouldBeCalled();

        $request->withAttribute(AuthenticatorManager::SKIPPED_AUTHENTICATORS_ATTRIBUTE_KEY, [])
            ->willReturn($request)
            ->shouldBeCalled();

        $serverRequest = $request->reveal();
        $authenticator->supports($serverRequest)->willReturn(true)->shouldBeCalled();

        $tokenStorage  = $this->prophesize(TokenStorageInterface::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->debug('Checking support on authenticator.', ['authenticator' => $authenticator->reveal()::class])
            ->shouldBeCalled();
        $logger->debug('Authenticator does not support the request.', ['authenticator' => $authenticator->reveal()::class])
            ->shouldNotBeCalled();

        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher, $logger->reveal());

        $this->assertTrue($authenticatorManager->supports($serverRequest));
    }

    #[Test]
    public function notSupports()
    {
        $hasher = $this->prophesize(PasswordHasherInterface::class)->reveal();
        $request = $this->prophesize(ServerRequestInterface::class);
        $authenticator = $this->prophesize(AuthenticatorInterface::class);

        $request->withAttribute(AuthenticatorManager::AUTHENTICATORS_ATTRIBUTE_KEY, [])
            ->willReturn($request)
            ->shouldBeCalled();

        $request->withAttribute(AuthenticatorManager::SKIPPED_AUTHENTICATORS_ATTRIBUTE_KEY, [$authenticator])
            ->willReturn($request)
            ->shouldBeCalled();

        $serverRequest = $request->reveal();
        $authenticator->supports($serverRequest)->willReturn(false)->shouldBeCalled();

        $tokenStorage  = $this->prophesize(TokenStorageInterface::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->debug('Checking support on authenticator.', ['authenticator' => $authenticator->reveal()::class])
            ->shouldBeCalled();
        $logger->debug('Authenticator does not support the request.', ['authenticator' => $authenticator->reveal()::class])
            ->shouldBeCalled();

        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher, $logger->reveal());

        $this->assertFalse($authenticatorManager->supports($serverRequest));
    }

    #[Test]
    public function invalidAuthenticator()
    {
        $hasher = $this->prophesize(PasswordHasherInterface::class)->reveal();
        $request = $this->prophesize(ServerRequestInterface::class);
        $tokenStorage  = $this->prophesize(TokenStorageInterface::class);
        $authenticatorManager = new AuthenticatorManager([(object)[]], $tokenStorage->reveal(), $hasher);
        $this->expectException(\InvalidArgumentException::class);
        $serverRequest = $request->reveal();
        $authenticatorManager->supports($serverRequest);
    }

    #[Test]
    public function authenticate()
    {
        $password = 'password';
        $hashedPassword = md5($password);

        list($hasher, $tokenStorage, $logger, $authenticator, $serverRequest, $token) = $this->createContext($hashedPassword, $password);
        $authenticator->onAuthenticationSuccess($serverRequest, $token->reveal())->willReturn(null)->shouldBeCalled();

        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher->reveal(), $logger->reveal());

        $this->assertNull($authenticatorManager->authenticateRequest($serverRequest));
    }

    #[Test]
    public function authenticateWithResponse()
    {
        $password = 'password';
        $hashedPassword = md5($password);
        $response = $this->prophesize(ResponseInterface::class);

        list($hasher, $tokenStorage, $logger, $authenticator, $serverRequest, $token) = $this->createContext($hashedPassword, $password);
        $authenticator->onAuthenticationSuccess($serverRequest, $token->reveal())->willReturn($response->reveal())->shouldBeCalled();

        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher->reveal(), $logger->reveal());

        $this->assertSame($response->reveal(), $authenticatorManager->authenticateRequest($serverRequest));
    }

    #[Test]
    public function invalidBadge()
    {
        $password = 'password';
        $hashedPassword = md5($password);
        $failingBadge = $this->prophesize(Passport\BadgeInterface::class);
        $failingBadge->isResolved()->willReturn(false);

        list($hasher, $tokenStorage, $logger, $authenticator, $serverRequest, $token, $passport) = $this->createContext($hashedPassword, $password);
        $passport->badges()->willReturn([$failingBadge->reveal()]);
        $authenticator->onAuthenticationSuccess($serverRequest, $token->reveal())->willReturn(null)->shouldNotBeCalled();
        $authenticator->authenticate($serverRequest)->willReturn($passport->reveal());
        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher->reveal(), $logger->reveal());
        $authenticator->onAuthenticationFailure($serverRequest, Argument::type(BadCredentialsException::class))
            ->willReturn(null)
            ->shouldBeCalled();
        $authenticatorManager->authenticateRequest($serverRequest);
    }

    #[Test]
    public function invalidBadgeWithResponse()
    {
        $password = 'password';
        $hashedPassword = md5($password);
        $failingBadge = $this->prophesize(Passport\BadgeInterface::class);
        $failingBadge->isResolved()->willReturn(false);
        $response = $this->prophesize(ResponseInterface::class);

        list($hasher, $tokenStorage, $logger, $authenticator, $serverRequest, $token, $passport) = $this->createContext($hashedPassword, $password);
        $passport->badges()->willReturn([$failingBadge->reveal()]);
        $authenticator->onAuthenticationSuccess($serverRequest, $token->reveal())->willReturn(null)->shouldNotBeCalled();
        $authenticator->authenticate($serverRequest)->willReturn($passport->reveal());
        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher->reveal(), $logger->reveal());
        $authenticator->onAuthenticationFailure($serverRequest, Argument::type(BadCredentialsException::class))
            ->willReturn($response->reveal())
            ->shouldBeCalled();
        $this->assertSame($response->reveal(), $authenticatorManager->authenticateRequest($serverRequest));
    }

    #[Test]
    public function noCredentialsBadge()
    {
        $password = 'password';
        $hashedPassword = md5($password);

        list($hasher, $tokenStorage, $logger, $authenticator, $serverRequest, $token, $passport) = $this->createContext($hashedPassword, $password);
        $passport->hasBadge(PasswordCredentials::class)->willReturn(false);
        $passport->badges()->willReturn([]);
        $authenticator->authenticate($serverRequest)->willReturn($passport->reveal());
        $authenticator->onAuthenticationSuccess($serverRequest, $token->reveal())->willReturn(null)->shouldBeCalled();
        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher->reveal(), $logger->reveal());
        $authenticator->onAuthenticationFailure(Argument::any(), Argument::any())
            ->willReturn(null)
            ->shouldNotBeCalled();
        $this->assertNull($authenticatorManager->authenticateRequest($serverRequest));
    }

    #[Test]
    public function credentialsNoPasswordUser()
    {
        $password = 'password';
        $hashedPassword = md5($password);
        $user = $this->prophesize(UserInterface::class);

        list($hasher, $tokenStorage, $logger, $authenticator, $serverRequest, $token, $passport) = $this->createContext($hashedPassword, $password);
        $passport->user()->willReturn($user->reveal());
        $authenticator->authenticate($serverRequest)->willReturn($passport->reveal());
        $authenticator->onAuthenticationSuccess($serverRequest, $token->reveal())->willReturn(null)->shouldNotBeCalled();

        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher->reveal(), $logger->reveal());
        $authenticator->onAuthenticationFailure($serverRequest, Argument::type(AuthenticationException::class))
            ->willReturn(null)
            ->shouldBeCalled();
        $this->assertNull($authenticatorManager->authenticateRequest($serverRequest));
    }

    #[Test]
    public function differentPassword()
    {
        $password = 'password';
        $hashedPassword = md5($password);

        list($hasher, $tokenStorage, $logger, $authenticator, $serverRequest) = $this->createContext($hashedPassword, $password);
        $hasher->verify($hashedPassword, $password)->willReturn(false);
        $authenticator->onAuthenticationSuccess($serverRequest, Argument::any())->willReturn(null)->shouldNotBeCalled();
        $authenticator->onAuthenticationFailure(Argument::any(), Argument::any())
            ->willReturn(null)
            ->shouldBeCalled();

        $authenticatorManager = new AuthenticatorManager([$authenticator->reveal()], $tokenStorage->reveal(), $hasher->reveal(), $logger->reveal());
        $this->assertNull($authenticatorManager->authenticateRequest($serverRequest));
        $this->assertCount(1, $authenticatorManager->authenticationErrors());
    }

    /**
     * @param string $hashedPassword
     * @param string $password
     * @return array
     * @throws \Slick\WebStack\Domain\Security\SecurityException
     */
    public function createContext(string $hashedPassword, string $password): array
    {
        $user = $this->prophesize(PasswordAuthenticatedUserInterface::class);
        $user->password()->willReturn($hashedPassword);

        $token = $this->prophesize(TokenInterface::class);

        $hasher = $this->prophesize(PasswordHasherInterface::class);
        $hasher->verify($hashedPassword, $password)->willReturn(true);

        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $logger = $this->prophesize(LoggerInterface::class);

        $passport = $this->prophesize(Passport::class);
        $passport->hasBadge(PasswordCredentials::class)->willReturn(true);
        $passwordCredentials = new PasswordCredentials($password);
        $passport->badge(PasswordCredentials::class)->willReturn($passwordCredentials);
        $passport->user()->willReturn($user->reveal());
        $passport->badges()->willReturn([$passwordCredentials]);

        $authenticator = $this->prophesize(AuthenticatorInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getAttribute(AuthenticatorManager::AUTHENTICATORS_ATTRIBUTE_KEY, [])->willReturn([$authenticator->reveal()]);
        $serverRequest = $request->reveal();

        $authenticator->authenticate($serverRequest)->willReturn($passport->reveal());
        $authenticator->createToken($passport)->willReturn($token->reveal());
        return array($hasher, $tokenStorage, $logger, $authenticator, $serverRequest, $token, $passport);
    }
}
