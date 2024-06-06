<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Authentication\Token\Storage;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * TokenStorage
 *
 * @package Slick\WebStack\Domain\Security\Authentication\Token\Storage
 * @template TUser of UserInterface
 * @implements TokenStorageInterface<TUser>
 */
final class TokenStorage implements TokenStorageInterface
{
    /**
     * @phpstan-var TokenInterface<TUser>|null
     */
    private ?TokenInterface $token = null;

    /**
     * @inheritDoc
     */
    public function getToken(): ?TokenInterface
    {
        return $this->token;
    }

    /**
     * @inheritDoc
     * @param TokenInterface<TUser> $token
     */
    public function setToken(?TokenInterface $token): void
    {
        $this->token = $token;
    }
}
