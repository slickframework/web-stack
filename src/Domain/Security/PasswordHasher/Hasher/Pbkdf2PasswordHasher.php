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
use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\PasswordHasher\LegacyPasswordHasherInterface;
use SensitiveParameter;
use function strlen;

/**
 * Pbkdf2PasswordHasher uses the PBKDF2 (Password-Based Key Derivation Function 2).
 *
 * Providing a high level of Cryptographic security,
 *   PBKDF2 is recommended by the National Institute of Standards and Technology (NIST).
 *
 * But also warrants a warning, using PBKDF2 (with a high number of iterations) slows down the process.
 *  PBKDF2 should be used with caution and care.
 *
 * @package Slick\WebStack\Domain\Security\PasswordHasher\Hasher
 */
final class Pbkdf2PasswordHasher implements LegacyPasswordHasherInterface
{
    use CheckPasswordLengthTrait;

    private int $encodedLength = -1;


    public function __construct(
        private readonly string $algorithm = 'sha512',
        private readonly int $iterations = 1000,
        private readonly int $length = 40,
        private readonly ?string $salt = null,
    ) {
        try {
            $this->encodedLength = strlen($this->hash('', 'salt'));
        } catch (\LogicException) {
            // ignore unsupported algorithm
        }
    }

    /**
     * @inheritDoc
     */
    public function hash(#[SensitiveParameter] string $plainPassword, ?string $salt = null): string
    {
        $salt = $salt ?: $this->salt;
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException("Password too long");
        }

        if (!in_array($this->algorithm, hash_algos(), true)) {
            throw new LogicException(sprintf('The algorithm "%s" is not supported.', $this->algorithm));
        }

        $digest = hash_pbkdf2($this->algorithm, $plainPassword, $salt ?? '', $this->iterations, $this->length, true);
        return base64_encode($digest);
    }

    /**
     * @inheritDoc
     */
    public function verify(
        string $hashedPassword,
        #[SensitiveParameter] string $plainPassword,
        ?string $salt = null
    ): bool {
        if (\strlen($hashedPassword) !== $this->encodedLength || str_contains($hashedPassword, '$')) {
            return false;
        }

        return !$this->isPasswordTooLong($plainPassword)
            && hash_equals($hashedPassword, $this->hash($plainPassword, $salt));
    }

    /**
     * @inheritDoc
     */
    public function needsRehash(string $hashedPassword): bool
    {
        return false;
    }

    public function info(): array
    {
        return [
            'algorithm' => $this->algorithm,
            'iterations' => $this->iterations,
            'length' => $this->encodedLength
        ];
    }
}
