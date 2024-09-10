<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Csrf\TokenStorage;

use Slick\WebStack\Domain\Security\Csrf\TokenStorage\SessionCsrfTokenStorage;
use Slick\WebStack\Domain\Security\Exception\CsrfTokenNotFound;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\Http\Session\SessionDriverInterface;

class SessionCsrfTokenStorageTest extends TestCase
{
    use ProphecyTrait;

    public function testSet(): void
    {
        $key = 'token';
        $nsKey = '_test_token';
        $tokenValue = 'a value';
        $session = $this->prophesize(SessionDriverInterface::class);
        $session->set($nsKey, $tokenValue)->shouldBeCalled()->willReturn($session->reveal());
        $storage = new SessionCsrfTokenStorage($session->reveal(), '_test');
        $storage->set($key, $tokenValue);
    }

    public function test__construct(): void
    {
        $session = $this->prophesize(SessionDriverInterface::class);
        $storage = new SessionCsrfTokenStorage($session->reveal());
        $this->assertInstanceOf(SessionCsrfTokenStorage::class, $storage);
    }

    public function testGet(): void
    {
        $key = 'token';
        $session = $this->prophesize(SessionDriverInterface::class);
        $tokenValue = "a value";
        $session->get(SessionCsrfTokenStorage::SESSION_NAMESPACE."_token")->willReturn($tokenValue);
        $storage = new SessionCsrfTokenStorage($session->reveal());
        $this->assertEquals($tokenValue, $storage->get($key));
    }

    public function testGetUnknownToken(): void
    {
        $key = 'token';
        $session = $this->prophesize(SessionDriverInterface::class);
        $session->get(SessionCsrfTokenStorage::SESSION_NAMESPACE."_token")->willReturn(null);
        $storage = new SessionCsrfTokenStorage($session->reveal());
        $this->expectException(CsrfTokenNotFound::class);
        $this->assertNull($storage->get($key));
    }

    public function testRemove(): void
    {
        $key = 'token';
        $nsKey = '_test_token';
        $session = $this->prophesize(SessionDriverInterface::class);
        $session->erase($nsKey)->shouldBeCalled()->willReturn($session->reveal());
        $storage = new SessionCsrfTokenStorage($session->reveal(), '_test');
        $storage->remove($key);
    }

    public function testHas(): void
    {
        $key = 'token';
        $session = $this->prophesize(SessionDriverInterface::class);
        $tokenValue = "a value";
        $session->get(SessionCsrfTokenStorage::SESSION_NAMESPACE."_token")->willReturn($tokenValue);
        $storage = new SessionCsrfTokenStorage($session->reveal());
        $this->assertTrue($storage->has($key));
    }
}
