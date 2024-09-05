<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * AuthenticatorInterface
 *
 * @package Slick\WebStack\Domain\Security\Http\Authenticator
 * @template-covariant TUser of UserInterface
 */
interface AuthenticatorInterface
{

    /**
     * Does the authenticator support the given Request?
     *
     * If this returns true, authenticate() will be called. If false, the authenticator will be skipped.
     *
     * Returning null means authenticate() can be called lazily when accessing the token storage.
     */
    public function supports(ServerRequestInterface $request): ?bool;

    /**
     * Create a passport for the current request.
     *
     * The passport contains the user, credentials and any additional information
     * that has to be checked by the Security system. For example, a login
     * form authenticator will probably return a passport containing the user, the
     * presented password and the CSRF token value.
     *
     * You may throw any AuthenticationException in this method in case of error (e.g.
     * a UserNotFoundException when the user cannot be found).
     *
     * @throws AuthenticationException|SecurityException
     * @return PassportInterface<TUser>
     */
    public function authenticate(ServerRequestInterface $request): PassportInterface;

    /**
     * Create an authenticated token for the given user.
     *
     *  If you don't care about which token class is used or don't really
     *  understand what a "token" is, you can skip this method by extending
     *  the AbstractAuthenticator class from your authenticator.
     *
     * @template T of UserInterface
     * @param PassportInterface<T> $passport
     * @return TokenInterface<TUser>
     */
    public function createToken(PassportInterface $passport): TokenInterface;

    /**
     * Called when authentication executed and was successful!
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the last page they visited.
     *
     * If you return null, the current request will continue, and the user
     * will be authenticated. This makes sense, for example, with an API.
     *
     * @template T of UserInterface
     * @param TokenInterface<T> $token
     */
    public function onAuthenticationSuccess(
        ServerRequestInterface $request,
        TokenInterface $token
    ): ?ResponseInterface;

    /**
     * Called when authentication executed, but failed (e.g. wrong username password).
     *
     * This should return the Response sent back to the user, like a
     * RedirectResponse to the login page or a 403 response.
     *
     * If you return null, the request will continue, but the user will
     * not be authenticated. This is probably not what you want to do.
     */
    public function onAuthenticationFailure(
        ServerRequestInterface $request,
        AuthenticationException $exception
    ): ?ResponseInterface;

    /**
     * Sets the handler for this authenticator.
     *
     * @param AuthenticatorHandlerInterface $handler The handler to set.
     *
     * @return self<TUser> The instance of this class.
     */
    public function withHandler(AuthenticatorHandlerInterface $handler): self;

    /**
     * Clears any cached data or state.
     *
     * This method is used to clear any cached data or state that the class may be holding.
     * It does not take any parameters and does not return any value.
     */
    public function clear(): void;
}
