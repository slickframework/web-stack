<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Exception\BadCredentialsException;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials\PasswordCredentials;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\User\PasswordAuthenticatedUserInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * AuthenticatorManager
 *
 * @package Slick\WebStack\Domain\Security\Http
 */
final class AuthenticatorManager implements AuthenticatorManagerInterface
{
    public const AUTHENTICATORS_ATTRIBUTE_KEY = '_security_authenticators';
    public const SKIPPED_AUTHENTICATORS_ATTRIBUTE_KEY = '_security_skipped_authenticators';

    /** @var array<string> */
    private array $errors = [];

    /**
     * Creates a AuthenticatorManager
     *
     * @template T of UserInterface
     * @param iterable<AuthenticatorInterface<T>> $authenticators
     * @param TokenStorageInterface<T> $tokenStorage
     * @param PasswordHasherInterface $hasher
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        private readonly iterable $authenticators,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly PasswordHasherInterface $hasher,
        private readonly ?LoggerInterface $logger = null
    ) {

    }

    /**
     * @inheritDoc
     * @throws InvalidArgumentException If at least one authenticator doesn't implement AuthenticatorInterface
     */
    public function supports(ServerRequestInterface &$request): ?bool
    {
        $authenticators = [];
        $skipped = [];
        foreach ($this->authenticators as $authenticator) {
            $this->logger?->debug('Checking support on authenticator.', ['authenticator' => $authenticator::class]);
            $this->checkAuthenticator($authenticator);

            if ($authenticator->supports($request)) {
                $authenticators[] = $authenticator;
                continue;
            }

            $skipped[] = $authenticator;
            $this->logger?->debug('Authenticator does not support the request.', ['authenticator' => $authenticator::class]);
        }

        $request = $request->withAttribute(self::AUTHENTICATORS_ATTRIBUTE_KEY, $authenticators);
        $request = $request->withAttribute(self::SKIPPED_AUTHENTICATORS_ATTRIBUTE_KEY, $skipped);

        return !empty($authenticators);
    }

    /**
     * @inheritDoc
     *
     * @throws SecurityException
     */
    public function authenticateRequest(ServerRequestInterface $request): ?ResponseInterface
    {
        $authenticators = $request->getAttribute(self::AUTHENTICATORS_ATTRIBUTE_KEY, []);
        foreach ($authenticators as $authenticator) {
            $response = $this->executeAuthenticator($authenticator, $request);
            if (null !== $response) {
                return $response;
            }
        }

        return null;
    }

    /**
     * Checks if the provided authenticator implements the AuthenticatorInterface.
     *
     * @param mixed $authenticator The authenticator to check.
     *
     * @throws InvalidArgumentException If the provided authenticator does not implement the AuthenticatorInterface.
     */
    public function checkAuthenticator(mixed $authenticator): void
    {
        if ($authenticator instanceof AuthenticatorInterface) {
            return;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Authenticator "%s" must implement "%s".',
                get_debug_type($authenticator),
                AuthenticatorInterface::class
            )
        );
    }

    /**
     * Executes the specified Authenticator with the given request.
     *
     * @param AuthenticatorInterface $authenticator The authenticator to execute.
     * @param ServerRequestInterface $request The request to authenticate.
     * @return ResponseInterface|null The response from the authenticator, or null if no response is returned.
     * @throws SecurityException
     *
     * @template T of UserInterface
     * @phpstan-param AuthenticatorInterface<T> $authenticator
     */
    private function executeAuthenticator(
        AuthenticatorInterface $authenticator,
        ServerRequestInterface $request
    ): ?ResponseInterface {

        try {
            // get the passport from the Authenticator
            $passport = $authenticator->authenticate($request);

            // check the passport (e.g. password checking)
            $this->checkPassportCredentials($passport);

            // check if all badges are resolved
            foreach ($passport->badges() as $badge) {
                if (!$badge->isResolved()) {
                    $lastNamespaceParts = explode('\\', get_debug_type($badge));
                    throw new BadCredentialsException(
                        sprintf(
                            'Authentication failed: Security badge "%s" is not resolved.',
                            end($lastNamespaceParts)
                        )
                    );
                }
            }
            // create the authentication token
            $authenticationToken = $authenticator->createToken($passport);

            $this->logger?->info(
                'Authenticator successful!',
                ['token' => $authenticationToken, 'authenticator' => $authenticator::class]
            );
        } catch (AuthenticationException $exception) {
            $response = $authenticator->onAuthenticationFailure($request, $exception);
            if ($exception->getCode() == 0) {
                $this->errors[$exception::class] = $exception->getMessage();
            }
            return  ($response instanceof ResponseInterface) ? $response : null;
        }

        // success! (sets the token on the token storage, etc)
        $this->tokenStorage->setToken($authenticationToken);
        $response = $authenticator->onAuthenticationSuccess($request, $authenticationToken);
        if ($response instanceof ResponseInterface) {
            return $response;
        }

        $this->logger?->debug(
            'Authenticator set no success response: request continues.',
            ['authenticator' => $authenticator::class]
        );

        return null;
    }

    /**
     * Checks the credentials in the passport.
     *
     * @template T of UserInterface
     * @param Authenticator\PassportInterface<T> $passport The authentication passport.
     *
     * @throws AuthenticationException When the user object does not implement PasswordAuthenticatedUserInterface.
     * @throws BadCredentialsException When the password couldn't be verified.
     * @throws SecurityException
     */
    private function checkPassportCredentials(Authenticator\PassportInterface $passport): void
    {
        if (!$passport->hasBadge(PasswordCredentials::class)) {
            return;
        }

        $user = $passport->user();
        if (!$user instanceof PasswordAuthenticatedUserInterface) {
            throw new AuthenticationException(
                sprintf(
                    'User object "%s" must implement "%s".',
                    get_debug_type($user),
                    PasswordAuthenticatedUserInterface::class
                )
            );
        }

        $hashedPassword = $user->password();
        /** @var PasswordCredentials $credentials */
        $credentials = $passport->badge(PasswordCredentials::class);
        if ($this->hasher->verify($hashedPassword, $credentials->password())) {
            $credentials->markResolved();
            return;
        }

        throw new BadCredentialsException("Password couldn't be verified.");
    }

    /**
     * @inheritDoc
     */
    
    public function authenticationErrors(): array
    {
        return $this->errors;
    }
}
