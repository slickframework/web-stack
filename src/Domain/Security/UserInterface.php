<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security;

/**
 * UserInterface
 *
 * @package Slick\WebStack\Domain\Security
 */
interface UserInterface
{

    /**
     * Returns the identifier of the user.
     *
     * @return string The identifier of the user
     */
    public function userIdentifier(): string;

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored in a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return string[]
     */
    public function roles(): array;
}
