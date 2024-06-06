<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Csrf;

use SensitiveParameter;
use Stringable;

/**
 * CsrfToken
 *
 * @package Slick\WebStack\Domain\Security\Csrf
 */
final readonly class CsrfToken implements Stringable
{
    /**
     * Creates a CsrfToken
     *
     * @param string $tokenId
     * @param string $value
     */
    public function __construct(
        private string $tokenId,
        #[SensitiveParameter]
        private string $value,
    ) {
    }

    /**
     * CsrfToken tokenId
     *
     * @return string
     */
    public function tokenId(): string
    {
        return $this->tokenId;
    }

    /**
     * CsrfToken value
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->value;
    }
}
