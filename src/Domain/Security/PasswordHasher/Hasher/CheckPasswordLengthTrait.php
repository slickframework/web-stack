<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\PasswordHasher\Hasher;

use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use SensitiveParameter;

/**
 * CheckPasswordLengthTrait
 *
 * @package Slick\WebStack\Domain\Security\PasswordHasher\Hasher
 */
trait CheckPasswordLengthTrait
{

    /**
     * Checks if the provided password is too long.
     *
     * @param string $password The password to be checked.
     * @return bool Returns true if the password is too long, false otherwise.
     */
    private function isPasswordTooLong(#[SensitiveParameter] string $password): bool
    {
        return PasswordHasherInterface::MAX_PASSWORD_LENGTH < strlen($password);
    }
}
