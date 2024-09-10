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
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use InvalidArgumentException;
use SensitiveParameter;
use function defined;
use function strlen;
use const PASSWORD_BCRYPT;

/**
 * PhpPasswordHasher
 *
 * @package Slick\WebStack\Domain\Security\PasswordHasher\Hasher
 */
final class PhpPasswordHasher implements PasswordHasherInterface
{
    use CheckPasswordLengthTrait;

    private string $algorithm = PASSWORD_BCRYPT;

    /**
     * @var array<string, mixed>|int[]
     */
    private array $options = [];

    /**
     * @var array<string, string>
     */
    private array $algorithms = [
        PASSWORD_BCRYPT => 'BCrypt',
        PASSWORD_ARGON2I => 'Argon 2I',
        PASSWORD_ARGON2ID => 'Argon 2ID',
    ];

    /**
     * Creates a PhpPasswordHasher
     *
     * @param int|null $opsLimit
     * @param int|null $memLimit
     * @param int|null $cost
     * @param string|null $algorithm
     */
    public function __construct(
        ?int $opsLimit = null,
        ?int $memLimit = null,
        ?int $cost = null,
        ?string $algorithm = null
    ) {
        $cost ??= 13;
        $opsLimit ??= 4;
        $memLimit ??= 64 * 1024 * 1024;


        if (3 > $opsLimit) {
            throw new InvalidArgumentException('$opsLimit must be 3 or greater.');
        }

        if (10 * 1024 > $memLimit) {
            throw new InvalidArgumentException('$memLimit must be 10k or greater.');
        }

        if ($cost < 4 || 31 < $cost) {
            throw new InvalidArgumentException('$cost must be in the range of 4-31.');
        }

        $algorithms = $this->getSupportedAlgorithms();
        if (null !== $algorithm) {
            $this->algorithm = $algorithms[$algorithm] ?? $algorithm;
        }

        $this->options = [
            'cost' => $cost,
            'time_cost' => $opsLimit,
            'memory_cost' => $memLimit >> 10,
            'threads' => 1,
        ];
    }

    /**
     * @inheritDoc
     */
    public function hash(#[SensitiveParameter] string $plainPassword): string
    {
        if ($this->isPasswordTooLong($plainPassword)) {
            throw new InvalidPasswordException("Password too long.");
        }

        $isBigOrHasNullByte = 72 < strlen($plainPassword) || str_contains($plainPassword, "\0");
        if (PASSWORD_BCRYPT === $this->algorithm && $isBigOrHasNullByte) {
            $plainPassword = base64_encode(hash('sha512', $plainPassword, true));
        }

        return password_hash($plainPassword, $this->algorithm, $this->options);
    }

    /**
     * @inheritDoc
     */
    public function verify(string $hashedPassword, #[SensitiveParameter] string $plainPassword): bool
    {
        if ('' === $plainPassword || $this->isPasswordTooLong($plainPassword)) {
            return false;
        }

        if (!str_starts_with($hashedPassword, '$argon')) {
            // Bcrypt cuts on NUL chars and after 72 bytes
            $isBigOrHasNullByte = 72 < strlen($plainPassword) || str_contains($plainPassword, "\0");
            if (str_starts_with($hashedPassword, '$2') && ($isBigOrHasNullByte)) {
                $plainPassword = base64_encode(hash('sha512', $plainPassword, true));
            }

            return password_verify($plainPassword, $hashedPassword);
        }

        return password_verify($plainPassword, $hashedPassword);
    }

    /**
     * @inheritDoc
     */
    public function needsRehash(string $hashedPassword): bool
    {
        return password_needs_rehash($hashedPassword, $this->algorithm, $this->options);
    }

    public function info(): array
    {
        return [
            'algorithm' => $this->algorithms[$this->algorithm],
            ...$this->options
        ];
    }

    /**
     * @return array<string|int, mixed>
     */
    public function getSupportedAlgorithms(): array
    {
        $algorithms = [1 => PASSWORD_BCRYPT, '2y' => PASSWORD_BCRYPT];
        $this->algorithm = PASSWORD_BCRYPT;

        if (defined('PASSWORD_ARGON2I')) {
            $algorithms[2] = $algorithms['argon2i'] = PASSWORD_ARGON2I;
            $this->algorithm = PASSWORD_ARGON2I;
        }

        if (defined('PASSWORD_ARGON2ID')) {
            $algorithms[3] = $algorithms['argon2id'] = PASSWORD_ARGON2ID;
            $this->algorithm = PASSWORD_ARGON2ID;
        }
        return $algorithms;
    }
}
