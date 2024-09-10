<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\RememberMe;

use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeDetails;
use Slick\WebStack\Domain\Security\Http\RememberMe\SignatureRememberMeHandler;
use Slick\WebStack\Domain\Security\Signature\SignatureHasher;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slick\Http\Message\Uri;
use Test\Slick\WebStack\Domain\Security\Signature\DummyUser;

class SignatureRememberMeHandlerTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $hasher = new SignatureHasher('someSecret');
        $userProvider = $this->prophesize(UserProviderInterface::class)->reveal();
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $signatureRememberMeHandler = new SignatureRememberMeHandler($hasher, $userProvider, $request);
        $this->assertInstanceOf(SignatureRememberMeHandler::class, $signatureRememberMeHandler);
    }

    #[Test]
    public function clearCookie()
    {
        $hasher = new SignatureHasher('someSecret');
        $userProvider = $this->prophesize(UserProviderInterface::class)->reveal();
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));
        $logger = $this->prophesize(LoggerInterface::class);
        $logger->debug('Clearing remember-me cookie.', ['cookieName' => 'REMEMBERME'])->shouldBeCalled();
        $signatureRememberMeHandler = new SignatureRememberMeHandler($hasher, $userProvider, $request->reveal(), logger: $logger->reveal());
        $signatureRememberMeHandler->clearRememberMeCookie();
    }

    #[Test]
    public function createCookie(): void
    {
        $hasher = new SignatureHasher('someSecret');
        $userProvider = $this->prophesize(UserProviderInterface::class)->reveal();
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));
        $signatureRememberMeHandler = new SignatureRememberMeHandler($hasher, $userProvider, $request->reveal());
        $signatureRememberMeHandler->createRememberMeCookie(new DummyUser());
        $this->assertInstanceOf(SignatureRememberMeHandler::class, $signatureRememberMeHandler);
    }

    #[Test]
    public function consumeRememberMeCookie(): void
    {
        $hasher = new SignatureHasher('someSecret');
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));

        $user = new DummyUser();
        $userIdentifier = 'userIdentifier';
        $expires = time() + 60 * 60;
        $value = $hasher->computeSignatureHash($user, $expires);
        $details = new RememberMeDetails(DummyUser::class, $userIdentifier, $expires, $value);

        $userProvider->loadUserByIdentifier($userIdentifier)->willReturn($user);

        $signatureRememberMeHandler = new SignatureRememberMeHandler($hasher, $userProvider->reveal(), $request->reveal());
        $retrievedUser = $signatureRememberMeHandler->consumeRememberMeCookie($details);
        $this->assertSame($user, $retrievedUser);
    }

    #[Test]
    public function consumeInvalidRememberMeCookie(): void
    {
        $hasher = new SignatureHasher('someSecret');
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));

        $user = new DummyUser();
        $userIdentifier = 'userIdentifier';
        $expires = time() + 60 * 60;
        $value = 'Test';
        $details = new RememberMeDetails(DummyUser::class, $userIdentifier, $expires, $value);

        $userProvider->loadUserByIdentifier($userIdentifier)->willReturn($user);

        $signatureRememberMeHandler = new SignatureRememberMeHandler($hasher, $userProvider->reveal(), $request->reveal());
        $this->expectException(AuthenticationException::class);
        $signatureRememberMeHandler->consumeRememberMeCookie($details);

    }

    #[Test]
    public function consumeExpiredRememberMeCookie(): void
    {
        $hasher = new SignatureHasher('someSecret');
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));

        $user = new DummyUser();
        $userIdentifier = 'userIdentifier';
        $expires = time() - 60 * 60;
        $value = $hasher->computeSignatureHash($user, $expires);;
        $details = new RememberMeDetails(DummyUser::class, $userIdentifier, $expires, $value);

        $userProvider->loadUserByIdentifier($userIdentifier)->willReturn($user);

        $signatureRememberMeHandler = new SignatureRememberMeHandler($hasher, $userProvider->reveal(), $request->reveal());
        $this->expectException(AuthenticationException::class);
        $signatureRememberMeHandler->consumeRememberMeCookie($details);
    }
}
