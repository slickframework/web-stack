<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\Authenticator;

use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\BadgeInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\SelfValidatingPassport;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SelfValidatingPassportTest extends TestCase
{
    use ProphecyTrait;

    public function test__construct(): void
    {
        $userBadge = $this->prophesize(UserBadge::class)->reveal();
        $otherBadge = $this->prophesize(BadgeInterface::class)->reveal();
        $passport = new SelfValidatingPassport($userBadge, [$otherBadge]);
        $this->assertInstanceOf(SelfValidatingPassport::class, $passport);
    }
}
