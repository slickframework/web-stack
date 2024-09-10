<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge;

use Slick\WebStack\Domain\Security\Exception\AuthenticationServiceException;
use Slick\WebStack\Domain\Security\Exception\BadCredentialsException;
use Slick\WebStack\Domain\Security\Exception\UserNotFoundException;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\BadgeInterface;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class UserBadgeTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function isInitializable()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $badge = new UserBadge('some-id', $userProvider->reveal());
        $this->assertInstanceOf(UserBadge::class, $badge);
        $this->assertInstanceOf(BadgeInterface::class, $badge);
    }

    #[Test]
    public function isResolved()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $badge = new UserBadge('some-id', $userProvider->reveal());
        $this->assertTrue($badge->isResolved());
    }

    #[Test]
    public function hasIdentifier()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $identifier = 'some-id';
        $badge = new UserBadge($identifier, $userProvider->reveal());
        $this->assertSame($identifier, $badge->userIdentifier());
    }

    #[Test]
    public function hasAUserProvider()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $provider = $userProvider->reveal();
        $badge = new UserBadge('some-id', $provider);
        $this->assertSame($provider, $badge->provider());
    }

    #[Test]
    public function itLoadsAUser()
    {
        $user = $this->prophesize(UserInterface::class);
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $userIdentifier = 'some-id';
        $userProvider->loadUserByIdentifier($userIdentifier)->willReturn($user->reveal());
        $badge = new UserBadge($userIdentifier, $userProvider->reveal());
        $this->assertSame($user->reveal(), $badge->user());
    }

    #[Test]
    public function loadFromCallable()
    {
        $user = $this->prophesize(UserInterface::class);
        $callable = fn () => $user->reveal();
        $userIdentifier = 'some-id';
        $badge = new UserBadge($userIdentifier, $callable);
        $this->assertSame($user->reveal(), $badge->user());
    }

    #[Test]
    public function loaderReturnsNull()
    {
        $callable = fn () => null;
        $userIdentifier = 'some-id';
        $badge = new UserBadge($userIdentifier, $callable);
        $this->expectException(UserNotFoundException::class);
        $badge->user();
    }

    #[Test]
    public function loaderReturnsObject()
    {
        $callable = fn () => (object)['name' => 'test'];
        $userIdentifier = 'some-id';
        $badge = new UserBadge($userIdentifier, $callable);
        $this->expectException(AuthenticationServiceException::class);
        $badge->user();
    }

    #[Test]
    public function longIdentifier()
    {
        $user = $this->prophesize(UserInterface::class);
        $callable = fn () => $user->reveal();
        $userIdentifier = $this->generateRandomString(UserBadge::MAX_USERNAME_LENGTH + 1);
        $this->expectException(BadCredentialsException::class);
        new UserBadge($userIdentifier, $callable);
    }

    private function generateRandomString($nameLength = 8): string
    {
        $alphaNumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        return substr(str_shuffle(str_repeat($alphaNumeric, $nameLength)), 0, $nameLength);
    }
}
