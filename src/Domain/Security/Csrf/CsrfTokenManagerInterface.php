<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Csrf;

/**
 * CsrfTokenManagerInterface
 *
 * @package Slick\WebStack\Domain\Security\Csrf
 */
interface CsrfTokenManagerInterface
{

    /**
     * Retrieves the CSRF token with the specified ID
     *
     * If previously no token existed for the given ID, a new token is
     * generated. Otherwise, the existing token is returned (with the same value,
     * not the same instance).
     *
     * @param string $tokenId The ID of the CSRF token
     * @return CsrfToken The CSRF token object
     */
    public function tokenWithId(string $tokenId): CsrfToken;

    /**
     * Generates a new token value for the given token ID.
     *
     * This method will generate a new token for the given token ID, independent
     * of whether a token value previously existed or not. It can be used to
     * enforce once-only tokens in environments with high security needs.
     *
     * @param string $tokenId The ID of the CSRF token to refresh.
     *
     * @return CsrfToken The refreshed CSRF token.
     */
    public function refreshToken(string $tokenId): CsrfToken;

    /**
     * Invalidates the CSRF token with the given ID, if one exists.
     *
     * This method will remove the CSRF token with the specified token ID. If the token
     * does not exist, no action will be taken. Use this method to clean up expired or
     * unused tokens.
     *
     * @param string $tokenId The ID of the CSRF token to remove.
     *
     * @return void
     */
    public function removeToken(string $tokenId): void;

    /**
     * Checks if the provided CSRF token is valid.
     *
     * @param CsrfToken $token The CSRF token to validate.
     *
     * @return bool Returns true if the CSRF token is valid, otherwise false.
     */
    public function isTokenValid(CsrfToken $token): bool;
}
