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
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * AbstractRememberMeHandler
 *
 * @package Slick\WebStack\Domain\Security\Http\RememberMe
 */
abstract class AbstractRememberMeHandler implements RememberMeHandlerInterface
{
    /** @var array<string, mixed>  */
    protected array $options = [];

    /**
     * Creates a AbstractRememberMeHandler
     *
     * @param UserProviderInterface $userProvider
     * @param ServerRequestInterface $request
     * @param array $options
     * @param LoggerInterface|null $logger
     *
     * @phpstan-template U of UserInterface
     * @phpstan-param array<string, mixed> $options
     * @phpstan-param UserProviderInterface<U> $userProvider
     */
    public function __construct(
        protected readonly UserProviderInterface $userProvider,
        protected readonly ServerRequestInterface $request,
        array $options = [],
        protected readonly ?LoggerInterface $logger = null
    ) {
        $this->options = $options + [
            'cookieName' => 'REMEMBERME',
            'lifetime' => 31536000,
            'path' => '/',
            'domain' => null,
            'secure' => false,
            'httponly' => true,
            'same_site' => null,
            'always_remember_me' => false,
            'remember_me_parameter' => '_remember_me',
        ];
    }

    /**
     * Checks if the RememberMeDetails is a valid cookie to log in the given User.
     *
     * This method should also:
     * - Create a new remember-me cookie to be sent with the response;
     * - If you store the token somewhere else (e.g. in a database), invalidate the stored token.
     *
     * @throws AuthenticationException If the remember-me details are not accepted
     */
    abstract protected function processRememberMe(
        RememberMeDetails $rememberMeDetails,
        UserInterface $user
    ): void;

    /**
     * Creates a remember me cookie.
     *
     * @param RememberMeDetails|null $rememberMeDetails The details for the cookie, or null to
     *                                                  clear the remember-me cookie.
     *
     * @return void
     */
    protected function createCookie(?RememberMeDetails $rememberMeDetails): void
    {
        $scheme = $this->request->getUri()->getScheme();
        setcookie(
            name: $this->options['cookieName'],
            value: $rememberMeDetails ? (string) $rememberMeDetails : '',
            expires_or_options: $rememberMeDetails?->expires() ?? 1,
            path: $this->options['path'] ?? "",
            domain: $this->options['domain'] ?? "",
            secure: $this->options['secure'] ?? $scheme == 'https',
            httponly: $this->options['httponly']
        );
    }

    /**
     * @inheritDoc
     * @throws SecurityException
     */
    public function consumeRememberMeCookie(RememberMeDetails $rememberMeDetails): UserInterface
    {
        $user = $this->userProvider->loadUserByIdentifier($rememberMeDetails->userIdentifier());
        $this->processRememberMe($rememberMeDetails, $user);

        $this->logger?->info('Remember-me cookie accepted.');

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function clearRememberMeCookie(): void
    {
        $this->logger?->debug('Clearing remember-me cookie.', ['cookieName' => $this->options['cookieName']]);
        $this->createCookie(null);
    }
}
