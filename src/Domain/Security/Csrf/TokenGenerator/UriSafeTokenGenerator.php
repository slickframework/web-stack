<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Csrf\TokenGenerator;

use Slick\WebStack\Domain\Security\Csrf\TokenGeneratorInterface;
use InvalidArgumentException;
use Random\RandomException;

/**
 * UriSafeTokenGenerator
 *
 * @package Slick\WebStack\Domain\Security\Csrf\TokenGenerator
 */
final class UriSafeTokenGenerator implements TokenGeneratorInterface
{

    /**
     * Generates URI-safe CSRF tokens.
     *
     * @param int $entropy The amount of entropy collected for each token (in bits)
     */
    public function __construct(private readonly int $entropy = 256)
    {
        if ($this->entropy <= 7) {
            throw new InvalidArgumentException('CSRF entropy should be greater than 7.');
        }
    }

    /**
     * @inheritDoc
     * @throws RandomException
     */
    public function generateToken(): string
    {
        // Generate a URI safe base64 encoded string that does not contain "+",
        // "/" or "=" which need to be URL encoded and make URLs unnecessarily
        // longer.
        /**
         * @template max
         * @phpstan-var int<1, max> $length
         */
        $length = intdiv($this->entropy, 8);
        $bytes = random_bytes($length);

        return rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');
    }
}
