<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Csrf;

use Slick\WebStack\Domain\Security\Csrf\CsrfToken;
use Slick\WebStack\Domain\Security\Csrf\CsrfTokenManager;
use Slick\WebStack\Domain\Security\Csrf\TokenGeneratorInterface;
use Slick\WebStack\Domain\Security\Csrf\TokenStorageInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class CsrfTokenManagerTest extends TestCase
{
    use ProphecyTrait;

    public function testInitializable()
    {
        $tokenGenerator = $this->prophesize(TokenGeneratorInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $manager = new CsrfTokenManager($tokenStorage->reveal(), $tokenGenerator->reveal());
        $this->assertInstanceOf(CsrfTokenManager::class, $manager);
    }

    public function testRemoveToken(): void
    {
        $tokenGenerator = $this->prophesize(TokenGeneratorInterface::class);
        $generatedToken = 'generated-token';
        $tokenGenerator->generateToken()->willReturn($generatedToken);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->has('tokenId')->willReturn(true);
        $tokenStorage->remove('tokenId')->shouldBeCalled();
        $manager = new CsrfTokenManager($tokenStorage->reveal(), $tokenGenerator->reveal());
        $manager->removeToken('tokenId');
    }

    public function testIsTokenValid(): void
    {
        $tokenGenerator = $this->prophesize(TokenGeneratorInterface::class);
        $generatedToken = 'generated-token';
        $tokenGenerator->generateToken()->willReturn($generatedToken);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->has('other')->willReturn(false);
        $tokenStorage->has('tokenId')->willReturn(false, true);
        $tokenStorage->set('tokenId', $generatedToken)->shouldBeCalled();
        $tokenStorage->get('tokenId')->willReturn($generatedToken);
        $manager = new CsrfTokenManager($tokenStorage->reveal(), $tokenGenerator->reveal());
        $csrfToken = $manager->tokenWithId('tokenId');
        $this->assertTrue($manager->isTokenValid($csrfToken));
        $this->assertFalse($manager->isTokenValid(new CsrfToken('other', 'val')));
    }

    public function testRefreshToken(): void
    {
        $tokenGenerator = $this->prophesize(TokenGeneratorInterface::class);
        $generatedToken = 'generated-token';
        $tokenGenerator->generateToken()->willReturn($generatedToken);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->set('tokenId', $generatedToken)->shouldBeCalled();
        $manager = new CsrfTokenManager($tokenStorage->reveal(), $tokenGenerator->reveal());
        $csrfToken = $manager->refreshToken('tokenId');
        $this->assertEquals($generatedToken, $csrfToken->value());
    }

    public function testTokenWithId(): void
    {
        $tokenGenerator = $this->prophesize(TokenGeneratorInterface::class);
        $generatedToken = 'generated-token';
        $tokenGenerator->generateToken()->willReturn($generatedToken);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->has('missing')->willReturn(false);
        $tokenStorage->has('existing')->willReturn(true);
        $tokenStorage->get('existing')->willReturn('some-value');
        $tokenStorage->set('missing', $generatedToken)->shouldBeCalled();
        $manager = new CsrfTokenManager($tokenStorage->reveal(), $tokenGenerator->reveal());
        $csrfToken = $manager->tokenWithId('missing');
        $this->assertEquals($generatedToken, $csrfToken->value());
        $this->assertEquals('some-value', $manager->tokenWithId('existing')->value());
    }
}
