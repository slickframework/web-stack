<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AuthorizationCheckerInterface
 *
 * @package Slick\WebStack\Domain\Security
 * @template-covariant TUser of UserInterface
 * @phpstan-template-covariant TUser of UserInterface
 */
interface AuthorizationCheckerInterface
{

    /**
     * Get the current authenticated user.
     *
     * @return UserInterface The authenticated user.
     * @phpstan-return TUser
     */
    public function authenticatedUser(): UserInterface;

    /**
     * Returns the access control list (ACL).
     *
     * @return array<string, string|array<string>> The access control list.
     */
    public function acl(): array;

    /**
     * Checks if the attribute is granted against the current authentication token.
     *
     * @param string|array<string> $attribute
     * @return bool
     */
    public function isGranted(string|array $attribute): bool;

    /**
     * Checks if the user is granted access based on current ACL permissions.
     *
     * @param ServerRequestInterface $request The server request.
     *
     * @return bool Returns true if the user is granted access, false otherwise.
     */
    public function isGrantedAcl(ServerRequestInterface $request): bool;

    /**
     * Processes the entry point for the given server request.
     *
     * If an entry point is defined, it will start the authentication process
     * using the provided server request. Otherwise, it returns a response with
     * a status code of 401 (Unauthorized).
     *
     * @param ServerRequestInterface $request The server request to process the entry point.
     * @return null|ResponseInterface Returns the response of the entry point or a response with
     *                           a status code of 401 (Unauthorized).
     */
    public function processEntryPoint(ServerRequestInterface $request): ?ResponseInterface;
}
