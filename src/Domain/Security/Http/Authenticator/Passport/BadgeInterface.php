<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\Authenticator\Passport;

/**
 * BadgeInterface
 *
 * @package Slick\WebStack\Domain\Security\Http\Authenticator\Passport
 */
interface BadgeInterface
{

    /**
     * Checks if this badge is resolved by the security system.
     *
     * After authentication, all badges must return `true` in this method in order
     * for the authentication to succeed.
     */
    public function isResolved(): bool;
}
