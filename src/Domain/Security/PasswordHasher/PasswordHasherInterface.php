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
 * PasswordHasherInterface
 *
 * @package Slick\WebStack\Domain\Security\Hasher
 */
interface PasswordHasherInterface
{

    public const MAX_PASSWORD_LENGTH = 4096;

    /**
     * Hashes a plain password.
     *
     * @throws InvalidPasswordException When the plain password is invalid, e.g. excessively long
     */
    public function hash(#[SensitiveParameter] string $plainPassword): string;

    /**
     * Verifies a plain password against a hash.
     */
    public function verify(string $hashedPassword, #[SensitiveParameter] string $plainPassword): bool;

    /**
     * Checks if a password hash would benefit from rehashing.
     */
    public function needsRehash(string $hashedPassword): bool;

    /**
     * Retrieves information about the hasher.
     *
     * @return array<string, mixed> The array containing the hasher information.
     */
    public function info(): array;
}
