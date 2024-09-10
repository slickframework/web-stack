<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\User;

use Slick\WebStack\Domain\Security\UserInterface;

/**
 * PasswordAuthenticatedUserInterface
 *
 * @package Slick\WebStack\Domain\Security\User
 */
interface PasswordAuthenticatedUserInterface extends UserInterface
{

    /**
     * Returns the hashed password used to authenticate the user.
     *
     * Usually on authentication, a plain-text password will be compared to this value.
     */
    public function password(): string;
}
