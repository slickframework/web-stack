<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Authentication\Token\TokenValidator;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenValidatorInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\UserNotFoundException;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\User\PasswordAuthenticatedUserInterface;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * UserIntegrityTokenValidator
 *
 * @package Slick\WebStack\Domain\Security\Authentication\Token\TokenValidator
 * @template-covariant TUser of UserInterface
 */
final class UserIntegrityTokenValidator implements TokenValidatorInterface
{

    /**
     * Creates a UserIntegrityTokenValidator
     *
     * @param UserProviderInterface<TUser> $provider
     */
    public function __construct(private readonly UserProviderInterface $provider)
    {

    }

    /**
     * @inheritDoc
     * @throws SecurityException
     * @throws UserNotFoundException
     */
    public function validate(TokenInterface $token): bool
    {
        $storedUser = $token->user();
        if (!$storedUser) {
            return false;
        }

        /** @var PasswordAuthenticatedUserInterface $user */
        $user = $this->provider->loadUserByIdentifier($storedUser->userIdentifier());

        if (
            $storedUser instanceof PasswordAuthenticatedUserInterface &&
            $user->password() !== $storedUser->password()
        ) {
            return false;
        }

        return $user && $user->roles() === $token->roleNames();
    }
}
