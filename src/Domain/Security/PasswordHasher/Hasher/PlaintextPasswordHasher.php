<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\PasswordHasher\Hasher;

use Slick\WebStack\Domain\Security\Exception\InvalidPasswordException;
use Slick\WebStack\Domain\Security\PasswordHasher\LegacyPasswordHasherInterface;
use InvalidArgumentException;
use SensitiveParameter;

/**
 * PlaintextPasswordHasher
 *
 * PlaintextPasswordHasher does not do any hashing but is useful in testing environments.
 * As this hasher is not cryptographically secure, usage of it in production environments is discouraged.
 *
 * @package Slick\WebStack\Domain\Security\PasswordHasher\Hasher
 */
final class PlaintextPasswordHasher implements LegacyPasswordHasherInterface
{
    use CheckPasswordLengthTrait;

    /**
     * @inheritDoc
     */
    public function hash(#[SensitiveParameter] string $plainPassword, ?string $salt = null): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException("Password is too long.");
        }
        return $this->mergePasswordAndSalt($plainPassword, $salt);
    }

    /**
     * @inheritDoc
     */
    public function verify(
        string $hashedPassword,
        #[SensitiveParameter]
        string $plainPassword,
        ?string $salt = null
    ): bool {
        if ($this->isPasswordTooLong($plainPassword)) {
            return false;
        }

        $hash = $this->mergePasswordAndSalt($plainPassword, $salt);
        return hash_equals($hash, $hashedPassword);
    }

    /**
     * @inheritDoc
     */
    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }

    /**
     * Merges password and salt and returns the merged string.
     *
     * @param string $password The password to merge.
     * @param string|null $salt The salt to merge.
     *
     * @return string The merged password and salt.
     *
     */
    private function mergePasswordAndSalt(#[SensitiveParameter] string $password, ?string $salt): string
    {
        if (empty($salt)) {
            return $password;
        }

        if (false !== strrpos($salt, '{') || false !== strrpos($salt, '}')) {
            throw new InvalidArgumentException('Cannot use { or } in salt.');
        }

        return $password.'{'.$salt.'}';
    }
}
