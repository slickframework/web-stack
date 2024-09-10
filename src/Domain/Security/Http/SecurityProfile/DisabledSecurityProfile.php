<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\SecurityProfile;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Session\SessionDriverInterface;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenValidatorInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfile\SessionSecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfileInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * DisabledSecurityProfile
 *
 * @package Slick\WebStack\Domain\Security\Http\SecurityProfile
 * @implements StatefulSecurityProfileInterface<UserInterface>
 */
final class DisabledSecurityProfile implements SecurityProfileInterface, StatefulSecurityProfileInterface
{

    use SecurityProfileTrait;

    /**
     * Creates a Disabled Security Profile
     *
     * @param string $matchExp The match expression.
     * @param TokenStorageInterface<UserInterface>|null $tokenStorage
     * @param SessionDriverInterface|null $session
     * @param TokenValidatorInterface|null $tokenValidator
     */
    public function __construct(
        string $matchExp,
        private readonly ?TokenStorageInterface $tokenStorage = null,
        private readonly ?SessionDriverInterface $session = null,
        private readonly ?TokenValidatorInterface $tokenValidator = null
    ) {
        $this->matchExp = $matchExp;
    }

    /**
     * @inheritDoc
     */
    
    public function process(ServerRequestInterface $request): ?ResponseInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    
    public function authenticationErrors(): array
    {
        return [];
    }

    public function restoreToken(): ?TokenInterface
    {


        if (!$token = $this->session?->get(SessionSecurityProfile::SESSION_KEY)) {
            return null;
        }

        if ($this->tokenValidator && !$this->tokenValidator->validate($token)) {
            return null;
        }


        $this->tokenStorage?->setToken($token);
        return $token;
    }

    /**
     * @inheritDoc
     */
    public function logout(): void
    {
        // nothing to do here.
    }
}
