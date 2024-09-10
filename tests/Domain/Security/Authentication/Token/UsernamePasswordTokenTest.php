<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Authentication\Token;

use Slick\WebStack\Domain\Security\Authentication\Token\AbstractToken;
use Slick\WebStack\Domain\Security\Authentication\Token\UsernamePasswordToken;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class UsernamePasswordTokenTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user = $this->prophesize(UserInterface::class);
        $token = new UsernamePasswordToken($user->reveal(), $roles);
        $this->assertInstanceOf(UsernamePasswordToken::class, $token);
        $this->assertInstanceOf(AbstractToken::class, $token);
        $this->assertInstanceOf(TokenInterface::class, $token);
    }

    #[Test]
    public function hasAUser()
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user = $this->prophesize(UserInterface::class);
        $token = new UsernamePasswordToken($user->reveal(), $roles);
        $this->assertSame($user->reveal(), $token->user());
    }

    #[Test]
    public function hasRoles()
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user = $this->prophesize(UserInterface::class);
        $token = new UsernamePasswordToken($user->reveal(), $roles);
        $this->assertEquals($roles, $token->roleNames());
    }

    #[Test]
    public function hasUserIdentifier()
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user = $this->prophesize(UserInterface::class);
        $identifier = 'Some-ID';
        $user->userIdentifier()->willReturn($identifier);
        $token = new UsernamePasswordToken($user->reveal(), $roles);
        $this->assertSame($identifier, $token->userIdentifier());
    }

    #[Test]
    public function convertToString()
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user = $this->prophesize(UserInterface::class);
        $identifier = 'Some-ID';
        $user->userIdentifier()->willReturn($identifier);
        $token = new UsernamePasswordToken($user->reveal(), $roles);
        $this->assertEquals('UsernamePasswordToken(user="Some-ID", roles="ROLE_USER, ROLE_ADMIN")', (string)$token);
    }

    #[Test]
    public function serializable()
    {
        $roles = ['ROLE_USER', 'ROLE_ADMIN'];
        $user = new TestUser();
        $token = new UsernamePasswordToken($user, $roles);
        $data = serialize($token);
        $unserializedToken = unserialize($data);
        $this->assertEquals($token, $unserializedToken);
        $this->assertEquals('UsernamePasswordToken(user="Some-test-ID", roles="ROLE_USER, ROLE_ADMIN")', (string) $unserializedToken);
    }
}
