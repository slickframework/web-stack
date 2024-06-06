<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator;

use Slick\WebStack\Domain\Security\Authentication\Token\UsernamePasswordToken;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials\PasswordCredentials;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorInterface;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slick\Http\Message\Response;

/**
 * HttpBasicAuthenticator
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator
 * @template TUser of UserInterface
 * @implements AuthenticatorInterface<TUser>
 */
final class HttpBasicAuthenticator implements AuthenticatorInterface, AuthenticationEntryPointInterface
{

    use AuthenticatorHandlerTrait;

    /**
     * Creates a HttpBasicAuthenticator
     *
     * @param string $realm
     * @param UserProviderInterface<TUser> $provider
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        private readonly string                $realm,
        private readonly UserProviderInterface $provider,
        private readonly ?LoggerInterface      $logger = null
    ) {
    }

    /**
     * @inheritDoc
     */
        public function supports(ServerRequestInterface $request): ?bool
    {
        $serverParams = $request->getServerParams() ?? [];
        return array_key_exists('PHP_AUTH_USER', $serverParams);
    }

    /**
     * @inheritDoc
     * @return Passport<UserInterface>
     */
        public function authenticate(ServerRequestInterface $request): Passport
    {
        $serverParams = $request->getServerParams() ?? [];
        $username = $serverParams['PHP_AUTH_USER'] ?? null;
        $password = $serverParams['PHP_AUTH_PW'] ?? '';

        $userBadge = new UserBadge($username, $this->provider->loadUserByIdentifier(...));
        $credentials = new PasswordCredentials($password);

        return new Passport($userBadge, $credentials);
    }

    /**
     * @inheritDoc
     * @phpstan-return UsernamePasswordToken<UserInterface>
     */
        public function createToken(PassportInterface $passport): UsernamePasswordToken
    {
        $authToken = new UsernamePasswordToken($passport->user(), $passport->user()->roles());
        $authToken->withAttributes([
            'IS_AUTHENTICATED_FULLY' => 'true',
            'IS_AUTHENTICATED_REMEMBERED' => 'false',
            'IS_AUTHENTICATED' => 'true'
        ]);
        return $authToken;
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
        public function onAuthenticationFailure(ServerRequestInterface $request, AuthenticationException $exception): ?ResponseInterface
    {
        $serverParams = $request->getServerParams() ?? [];
        $this->logger?->info(
            'Basic authentication failed for user.',
            [
                'username' => $serverParams['PHP_AUTH_USER'] ?? '',
                'exception' => $exception,
            ]
        );

        return $this->start($request);
    }

    /**
     * @inheritDoc
     */
        public function start(ServerRequestInterface $request, ?AuthenticationException $authException = null): ResponseInterface
    {
        return new Response(401, 'Unauthorized', ['WWW-Authenticate' => sprintf('Basic realm="%s"', $this->realm)]);
    }
}
