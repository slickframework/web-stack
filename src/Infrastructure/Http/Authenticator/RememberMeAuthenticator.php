<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator;

use Slick\WebStack\Domain\Security\Authentication\Token\RememberMeToken;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Exception\UserNotFoundException;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\SelfValidatingPassport;
use Slick\WebStack\Domain\Security\Http\AuthenticatorInterface;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeDetails;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeHandlerInterface;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use SensitiveParameter;

/**
 * RememberMeAuthenticator
 *
 * @package Slick\WebStack\Domain\Security\Http
 *
 * @template-covariant TUser of UserInterface
 * @implements AuthenticatorInterface<TUser>
 */
final class RememberMeAuthenticator implements AuthenticatorInterface
{
    use AuthenticatorHandlerTrait;

    public const COOKIE_NAME = 'remember_me';

    /**
     * Creates a RememberMeAuthenticator
     *
     * @param RememberMeHandlerInterface $rememberMeHandler
     * @param string $secret
     * @param TokenStorageInterface<TUser> $tokenStorage
     * @param string|null $cookieName
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        private readonly RememberMeHandlerInterface $rememberMeHandler,
        #[SensitiveParameter]
        private readonly string $secret,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly ?string $cookieName = self::COOKIE_NAME,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function supports(ServerRequestInterface $request): bool
    {
        if (null !== $this->tokenStorage->getToken()) {
            return false;
        }

        $cookies = $request->getCookieParams();
        if (!array_key_exists($this->cookieName ?? '', $cookies)) {
            return false;
        }

        $this->logger?->debug('Remember-me cookie detected.');
        return true;
    }

    /**
     * @inheritDoc
     * @return SelfValidatingPassport<UserInterface>
     */
    public function authenticate(ServerRequestInterface $request): SelfValidatingPassport
    {
        $cookies = $request->getCookieParams();
        $rememberMeCookie = RememberMeDetails::fromRawCookie($cookies[$this->cookieName] ?? '');
        $userBadge = new Passport\Badge\UserBadge(
            $rememberMeCookie->userIdentifier(),
            fn () => $this->rememberMeHandler->consumeRememberMeCookie($rememberMeCookie)
        );

        return new SelfValidatingPassport($userBadge);
    }

    /**
     * @inheritDoc
     * @return RememberMeToken
     * @throws SecurityException
     */
    public function createToken(PassportInterface $passport): TokenInterface
    {
        $rememberMeToken = new RememberMeToken($passport->user(), $this->secret);
        $rememberMeToken->withAttributes([
            'IS_AUTHENTICATED_FULLY' => 'false',
            'IS_AUTHENTICATED_REMEMBERED' => 'true',
            'IS_AUTHENTICATED' => 'true'
        ]);
        return $rememberMeToken;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(ServerRequestInterface $request, TokenInterface $token): ?ResponseInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(
        ServerRequestInterface $request,
        AuthenticationException $exception
    ): ?ResponseInterface {
        if ($exception instanceof UserNotFoundException) {
            $this->logger?->info('User for remember-me cookie not found.', ['exception' => $exception]);
            return null;
        }

        $this->logger?->debug('Remember me authentication failed.', ['exception' => $exception]);

        return null;
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        $this->rememberMeHandler->clearRememberMeCookie();
    }
}
