<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfile;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenValidatorInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorManagerInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\SecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfileInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Session\SessionDriverInterface;

/**
 * SessionSecurityProfile
 *
 * @package Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfile
 * @template-covariant T of UserInterface
 * @implements StatefulSecurityProfileInterface<T>
 */
final class SessionSecurityProfile extends SecurityProfile implements StatefulSecurityProfileInterface
{
    public const SESSION_KEY = '_security_session_token';

    /**
     * Creates a Session Security Profile
     *
     * @param string $matchExp The match expression for the HTTP middleware process
     * @param AuthenticatorManagerInterface $authenticatorManager The authenticator manager
     * @param TokenStorageInterface<T> $tokenStorage The token storage
     * @param SessionDriverInterface $session The session driver
     * @param AuthenticationEntryPointInterface|null $entryPoint The authentication entry point (optional)
     */
    public function __construct(
        string $matchExp,
        AuthenticatorManagerInterface $authenticatorManager,
        TokenStorageInterface $tokenStorage,
        private readonly SessionDriverInterface $session,
        ?AuthenticationEntryPointInterface $entryPoint = null,
        private readonly ?TokenValidatorInterface $tokenValidator = null
    ) {
        parent::__construct($matchExp, $authenticatorManager, $tokenStorage, $entryPoint);
    }

    /**
     * Restores the session token and stores it in the token storage
     *
     * @return TokenInterface|null The restored token or null if session token is not found
     *
     * @phpstan-return TokenInterface<T> $token
     */
    public function restoreToken(): ?TokenInterface
    {
        if (!$token = $this->session->get(self::SESSION_KEY)) {
            return null;
        }

        if ($this->tokenValidator && !$this->tokenValidator->validate($token)) {
            return null;
        }


        $this->tokenStorage->setToken($token);
        return $token;
    }

    /**
     * @inheritDoc
     */
    
    protected function handleAuthenticationSuccess(ServerRequestInterface &$request): void
    {
        parent::handleAuthenticationSuccess($request);
        $this->session->set(self::SESSION_KEY, $this->tokenStorage->getToken());
    }
}
