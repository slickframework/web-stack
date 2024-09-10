<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\SecurityProfile;

use Slick\WebStack\Domain\Security\Http\SecurityProfile\DisabledSecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\SecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfileInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class DisabledSecurityProfileTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $profile = new DisabledSecurityProfile('^/api');
        $this->assertInstanceOf(DisabledSecurityProfile::class, $profile);
        $this->assertInstanceOf(SecurityProfileInterface::class, $profile);
    }

    #[Test]
    public function match()
    {
        $profile = new DisabledSecurityProfile('/^\/api(.*)/i');
        $request = $this->prophesize(ServerRequestInterface::class);
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()->willReturn('/api/test');
        $request->getUri()->willReturn($uri->reveal());
        $this->assertTrue($profile->match($request->reveal()));
    }

    #[Test]
    public function process()
    {
        $profile = new DisabledSecurityProfile('/^\/api(.*)/i');
        $request = $this->prophesize(ServerRequestInterface::class);
        $this->assertNull($profile->process($request->reveal()));
    }

    #[Test]
    public function itHasAListOfErrors(): void
    {
        $profile = new DisabledSecurityProfile('/^\/api(.*)/i');
        $this->assertEmpty($profile->authenticationErrors());
    }
}
