<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\RememberMe;

use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * RememberMeHandlerInterface
 *
 * @package Slick\WebStack\Domain\Security\Http\RememberMe
 */
interface RememberMeHandlerInterface
{

    /**
     * Creates a remember-me cookie.
     */
    public function createRememberMeCookie(UserInterface $user): void;

    /**
     * Validates the remember-me cookie and returns the associated User.
     *
     * Every cookie should only be used once. This means that this method should also:
     * - Create a new remember-me cookie to be sent with the response;
     * - If you store the token somewhere else (e.g. in a database), invalidate the
     *   stored token.
     *
     * @throws AuthenticationException
     */
    public function consumeRememberMeCookie(RememberMeDetails $rememberMeDetails): UserInterface;

    /**
     * Clears the remember-me cookie.
     *
     * This should set a cookie with a `null` value.
     */
    public function clearRememberMeCookie(): void;
}
