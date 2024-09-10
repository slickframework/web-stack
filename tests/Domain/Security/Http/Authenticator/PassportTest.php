<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\Authenticator;

use Slick\WebStack\Domain\Security\Http\Authenticator\Passport;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Slick\WebStack\Domain\Security\UserInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PassportTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $userBadge = $this->prophesize(UserBadge::class);
        $credentials = $this->prophesize(Passport\Badge\Credentials\PasswordCredentials::class);
        $other = $this->prophesize(Passport\BadgeInterface::class);

        $passport = new Passport($userBadge->reveal(), $credentials->reveal(), [$other->reveal()]);
        $this->assertInstanceOf(Passport::class, $passport);
    }

    #[Test]
    public function retrieveUser()
    {
        $userBadge = $this->prophesize(UserBadge::class);
        $user = $this->prophesize(UserInterface::class);
        $userBadge->user()->willReturn($user->reveal());
        $credentials = $this->prophesize(Passport\Badge\Credentials\PasswordCredentials::class);
        $other = $this->prophesize(Passport\BadgeInterface::class);

        $passport = new Passport($userBadge->reveal(), $credentials->reveal(), [$other->reveal()]);
        $this->assertSame($user->reveal(), $passport->user());
    }


    #[Test]
    public function hasBadge()
    {
        $userBadge = $this->prophesize(UserBadge::class);
        $credentials = $this->prophesize(Passport\Badge\Credentials\PasswordCredentials::class);
        $other = $this->prophesize(Passport\BadgeInterface::class);

        $otherBadge = $other->reveal();
        $passport = new Passport($userBadge->reveal(), $credentials->reveal(), [$otherBadge]);
        $this->assertTrue($passport->hasBadge($otherBadge::class));
    }

    #[Test]
    public function badges()
    {
        $userBadge = $this->prophesize(UserBadge::class);
        $credentials = $this->prophesize(Passport\Badge\Credentials\PasswordCredentials::class);
        $other = $this->prophesize(Passport\BadgeInterface::class);
        $passport = new Passport($userBadge->reveal(), $credentials->reveal(), [$other->reveal()]);
        $this->assertEquals(
            [$userBadge->reveal(), $credentials->reveal(), $other->reveal()],
            array_values($passport->badges())
        );
    }

}
