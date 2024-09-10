<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Authentication\Token\Storage;

use Slick\WebStack\Domain\Security\Authentication\Token\Storage\TokenStorage;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class TokenStorageTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $storage = new TokenStorage();
        $this->assertInstanceOf(TokenStorage::class, $storage);
    }

    #[Test]
    public function hasAToken()
    {
        $storage = new TokenStorage();
        $this->assertNull($storage->getToken());

        $token = $this->prophesize(TokenInterface::class);
        $storage->setToken($token->reveal());
        $this->assertSame($token->reveal(), $storage->getToken());
    }
}
