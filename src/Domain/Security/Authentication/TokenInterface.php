<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Authentication;

use Slick\WebStack\Domain\Security\Common\AttributesBagInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Stringable;

/**
 * TokenInterface
 *
 * @package Slick\WebStack\Domain\Security\Authentication
 * @template-covariant TUser of UserInterface
 */
interface TokenInterface extends Stringable, AttributesBagInterface
{

    /**
     * Returns the user identifier used during authentication (e.g. a user's email address or username).
     */
    public function userIdentifier(): string;

    /**
     * Returns the user roles.
     *
     * @return array<string>|string[]
     */
    public function roleNames(): array;

    /**
     * Returns a user representation.
     * @phpstan-return TUser|null
     */
    public function user(): ?UserInterface;

    /**
     * Returns all the necessary state of the object for serialization purposes.
     */
    public function __serialize(): array;

    /**
     * Restores the object state from an array given by __serialize().
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void;
}
