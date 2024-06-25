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

/**
 * TokenStorageInterface
 *
 * @package Slick\WebStack\Domain\Security\Csrf
 */
interface TokenStorageInterface
{

    /**
     * Retrieves the value for the specified token ID.
     *
     * @param string $tokenId The ID of the token.
     * @return string The value associated with the specified token ID.
     */
    public function get(string $tokenId): string;

    /**
     * Sets the value for the specified token ID.
     *
     * @param string $tokenId The ID of the token.
     * @param string $token The value of the token. Please note that the $token is sensitive and
     * should be treated with caution.
     * @return void
     */
    public function set(string $tokenId, #[SensitiveParameter] string $token): void;

    /**
     * Removes the value for the specified token ID.
     *
     * @param string $tokenId The ID of the token to be removed.
     * @return void
     */
    public function remove(string $tokenId): void;

    /**
     * Checks if there is a value associated with the specified token ID.
     *
     * @param string $tokenId The ID of the token.
     * @return bool True if there is a value associated with the specified token ID, false otherwise.
     */
    public function has(string $tokenId): bool;
}
