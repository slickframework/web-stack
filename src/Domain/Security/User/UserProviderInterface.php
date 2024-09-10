<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\User;

use Slick\WebStack\Domain\Security\Exception\UserNotFoundException;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * UserProviderInterface
 *
 * @package Slick\WebStack\Domain\Security\User
 * @template-covariant TUser of UserInterface
 * @phpstan-template-covariant TUser of UserInterface
 */
interface UserProviderInterface
{

    /**
     * Loads the user for the given user identifier (e.g. username or email).
     *
     * This method must throw UserNotFoundException if the user is not found.
     *
     * @param string $identifier
     * @throws UserNotFoundException
     * @throws SecurityException
     * @phpstan-return TUser
     */
    public function loadUserByIdentifier(string $identifier): UserInterface;
}
