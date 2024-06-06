<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Authentication\Token\TokenValidator;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenValidator\UserIntegrityTokenValidator;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenValidatorInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\User\PasswordAuthenticatedUserInterface;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class UserIntegrityTokenValidatorTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initialize()
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $validator = new UserIntegrityTokenValidator($userProvider->reveal());
        $this->assertInstanceOf(UserIntegrityTokenValidator::class, $validator);
        $this->assertInstanceOf(TokenValidatorInterface::class, $validator);
    }

    #[Test]
    public function validateUserPassword()
    {
        $user = $this->prophesize(PasswordAuthenticatedUserInterface::class);
        $user->password()->willReturn(md5('password2'));

        $identifier = 'some-id';
        $storedUser = $this->prophesize(PasswordAuthenticatedUserInterface::class);
        $storedUser->password()->willReturn(md5('password1'));
        $storedUser->userIdentifier()->willReturn($identifier);


        $userProvider = $this->prophesize(UserProviderInterface::class);
        $userProvider->loadUserByIdentifier($identifier)->willReturn($user->reveal());

        $token = $this->prophesize(TokenInterface::class);
        $token->user()->willReturn($storedUser->reveal());

        $validator = new UserIntegrityTokenValidator($userProvider->reveal());
        $this->assertFalse($validator->validate($token->reveal()));
    }

    #[Test]
    public function validateUserRoles()
    {
        $user = $this->prophesize(PasswordAuthenticatedUserInterface::class);
        $user->password()->willReturn(md5('password1'));
        $user->roles()->willReturn(['ROLE_USER', 'ROLE_ADMIN']);

        $identifier = 'some-id';
        $storedUser = $this->prophesize(PasswordAuthenticatedUserInterface::class);
        $storedUser->password()->willReturn(md5('password1'));
        $storedUser->userIdentifier()->willReturn($identifier);

        $userProvider = $this->prophesize(UserProviderInterface::class);
        $userProvider->loadUserByIdentifier($identifier)->willReturn($user->reveal());

        $token = $this->prophesize(TokenInterface::class);
        $token->user()->willReturn($storedUser->reveal());
        $token->roleNames()->willReturn(['ROLE_USER']);

        $validator = new UserIntegrityTokenValidator($userProvider->reveal());
        $this->assertFalse($validator->validate($token->reveal()));
    }

    #[Test]
    public function itDoesNotValidateOnMissingUser(): void
    {
        $userProvider = $this->prophesize(UserProviderInterface::class);
        $token = $this->prophesize(TokenInterface::class);
        $token->user()->willReturn(null);

        $validator = new UserIntegrityTokenValidator($userProvider->reveal());
        $this->assertFalse($validator->validate($token->reveal()));
    }
}
