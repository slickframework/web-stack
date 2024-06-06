<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\PasswordHasher;

use Slick\WebStack\Domain\Security\Exception\InvalidPasswordException;
use SensitiveParameter;

/**
 * LegacyPasswordHasherInterface
 *
 * @package Slick\WebStack\Domain\Security\PasswordHasher
 */
interface LegacyPasswordHasherInterface extends PasswordHasherInterface
{
    /**
     * Hashes a plain password.
     *
     * @throws InvalidPasswordException If the plain password is invalid, e.g. excessively long
     */
    public function hash(#[SensitiveParameter] string $plainPassword, ?string $salt = null): string;

    /**
     * Checks that a plain password and a salt match a password hash.
     */
    public function verify(string $hashedPassword, #[SensitiveParameter] string $plainPassword, ?string $salt = null): bool;

}
