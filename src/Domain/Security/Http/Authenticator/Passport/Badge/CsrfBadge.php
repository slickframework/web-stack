<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge;

use Slick\WebStack\Domain\Security\Csrf\CsrfToken;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\BadgeInterface;

/**
 * CsrfBadge
 *
 * @package Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge
 */
final class CsrfBadge implements BadgeInterface
{
    private mixed $validatingFn;
    private CsrfToken $csrfToken;

    public function __construct(CsrfToken $csrfToken, callable $validatingFn)
    {
        $this->validatingFn = $validatingFn;
        $this->csrfToken = $csrfToken;
    }

    /**
     * @inheritDoc
     */
    public function isResolved(): bool
    {
        $function = $this->validatingFn;
        return $function($this->csrfToken);
    }
}
