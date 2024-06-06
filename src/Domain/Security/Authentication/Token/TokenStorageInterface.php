<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Authentication\Token;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * TokenStorageInterface
 *
 * @package Slick\WebStack\Domain\Security\Authentication\Token
 * @template TUser of UserInterface
 */
interface TokenStorageInterface
{

    /**
     * Returns the current security token.
     *
     * @return null|TokenInterface<TUser>
     */
    public function getToken(): ?TokenInterface;

    /**
     * Sets the authentication token.
     * @template T of UserInterface
     *
     * @param TokenInterface<T>|null $token A TokenInterface token, or null if no further authentication information should be stored
     */
    public function setToken(?TokenInterface $token): void;
}
