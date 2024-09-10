<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AuthenticatorManagerInterface
 *
 * @package Slick\WebStack\Domain\Security\Http
 */
interface AuthenticatorManagerInterface
{

    /**
     * Called to see if authentication should be attempted on this request.
     */
    public function supports(ServerRequestInterface &$request): ?bool;

    /**
     * Tries to authenticate the request and returns a response - if any authenticator set one.
     */
    public function authenticateRequest(ServerRequestInterface $request): ?ResponseInterface;

    /**
     * Returns an array of authentication errors.
     *
     * @return array<string> An array of authentication errors, if any.
     */
    public function authenticationErrors(): array;

    /**
     * Clears any data or state associated with the current object.
     */
    public function clear(): void;
}
