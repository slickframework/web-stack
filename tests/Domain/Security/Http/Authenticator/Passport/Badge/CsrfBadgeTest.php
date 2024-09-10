<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge;

use Slick\WebStack\Domain\Security\Csrf\CsrfToken;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\CsrfBadge;
use PHPUnit\Framework\TestCase;

class CsrfBadgeTest extends TestCase
{

    public function test__construct()
    {
        $token = new CsrfToken('test', 'value');
        $callable = fn () => true;
        $badge = new CsrfBadge($token, $callable);
        $this->assertInstanceOf(CsrfBadge::class, $badge);
    }

    public function testIsResolved()
    {
        $token = new CsrfToken('test', 'value');
        $callable = fn () => true;
        $badge = new CsrfBadge($token, $callable);
        $this->assertTrue($badge->isResolved());
    }
}
