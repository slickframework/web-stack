<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\Authenticator;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AuthenticatorHandlerInterface
 *
 * @package App\Infrastructure\Http\Authenticator\FormLoginAuthenticator
 */
interface AuthenticatorHandlerInterface
{
    /**
     * Handles the success of the authentication process.
     *
     * @template TUser of UserInterface
     * @param ServerRequestInterface $request The incoming request object
     * @param TokenInterface<TUser> $token The authenticated token
     *
     * @return ResponseInterface|null The response object if available, otherwise null
     */
    public function onAuthenticationSuccess(ServerRequestInterface $request, TokenInterface $token): ?ResponseInterface;

    /**
     * Handles the case when authentication fails.
     *
     * @param ServerRequestInterface $request The server request of the authentication failure
     * @param AuthenticationException $exception The authentication exception that caused the failure
     *
     * @return ResponseInterface|null The response to be sent back to the client, or null if no
     * response needs to be sent
     */
    public function onAuthenticationFailure(
        ServerRequestInterface $request,
        AuthenticationException $exception
    ): ?ResponseInterface;

    /**
     * Authenticates the user and returns the authenticated passport.
     *
     * @template TUser of UserInterface
     * @param ServerRequestInterface $request The server request for authentication
     * @param PassportInterface<TUser> $passport The passport containing the user's credentials for authentication
     *
     * @return PassportInterface<TUser> The authenticated passport
     */
    public function onAuthenticate(ServerRequestInterface $request, PassportInterface $passport): PassportInterface;
}
