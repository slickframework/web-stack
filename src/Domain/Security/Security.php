<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security;

use Psr\Container\ContainerExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Di\Exception\NotFoundException;
use Slick\Http\Session\SessionDriverInterface;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\Token\UserToken;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\UserNotFoundException;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\SecurityProfileTrait;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfile\SessionSecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfileInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfileFactory;

/**
 * Security
 *
 * @template-covariant TUser of UserInterface
 * @package Slick\WebStack\Domain\Security
 *
 * @template-implements AuthorizationCheckerInterface<TUser>
 */
final class Security implements AuthorizationCheckerInterface, SecurityAuthenticatorInterface
{
    use SecurityProfileTrait;

    /**
     * @var array<string, mixed>
     */
    private static array $defaultOptions = [
        'enabled' => false,
        'accessControl' => []
    ];

    /**
     * @var array<string, mixed>
     */
    private array $options;

    /**
     * @var array<string>
     */
    private array $errors = [];
    private ?Http\SecurityProfileInterface $securityProfile = null;

    /**
     * Creates a Security
     *
     * @param SecurityProfileFactory $profileFactory
     * @param TokenStorageInterface<TUser> $tokenStorage
     * @param array<string, mixed> $options
     * @param SessionDriverInterface|null $sessionDriver
     */
    public function __construct(
        private readonly SecurityProfileFactory $profileFactory,
        private readonly TokenStorageInterface $tokenStorage,
        array $options = [],
        private readonly ?SessionDriverInterface $sessionDriver = null
    ) {
        $this->options = array_merge(self::$defaultOptions, $options);
    }

    /**
     * @inheritDoc
     */
    
    public function isGranted(string|array $attribute): bool
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return false;
        }

        if (is_array($attribute)) {
            return $this->checkAttributesList($attribute);
        }

        return $this->checkRole($token, $attribute) || $this->checkTokenAttributes($token, $attribute);
    }

    /**
     * @inheritDoc
     */
    
    public function isGrantedAcl(ServerRequestInterface $request): bool
    {
        if (count($this->acl()) > 0 && !$this->tokenStorage->getToken()) {
            return false;
        }

        foreach ($this->acl() as $pattern => $attributes) {
            $this->matchExp = $pattern;
            if ($this->match($request)) {
                return $this->isGranted($attributes);
            }
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    
    public function enabled(): bool
    {
        return $this->options['enabled'];
    }

    /**
     * @inheritDoc
     */
    
    public function acl(): array
    {
        return $this->options['accessControl'];
    }

    /**
     * @inheritDoc
     * @throws NotFoundException|ContainerExceptionInterface
     */
    public function process(ServerRequestInterface $request): ?ResponseInterface
    {
        $securityProfile = $this->profileFactory->createProfile($this->options, $request);
        if ($securityProfile) {
            $this->securityProfile = $securityProfile;
            $this->options['accessControl'] = $securityProfile->acl();
            if ($securityProfile instanceof StatefulSecurityProfileInterface &&
                $securityProfile->restoreToken()
            ) {
                return null;
            }

            $process = $securityProfile->process($request);
            $this->errors = $securityProfile->authenticationErrors();
            return $process;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     * @return TUser|UserInterface
     */
    
    public function authenticatedUser(): UserInterface
    {
        $user = $this->user();
        if ($user instanceof UserInterface) {
            return $user;
        }

        throw new UserNotFoundException('User not authenticated.');
    }

    /**
     * Checks if given token has a specific attribute.
     *
     * @param TokenInterface $token The token to check.
     * @param string $attribute The attribute to look for.
     *
     * @template T of UserInterface
     * @phpstan-param TokenInterface<T> $token
     *
     * @return bool Returns true if the token has the attribute, false otherwise.
     */
    public function checkTokenAttributes(TokenInterface $token, string $attribute): bool
    {
        foreach ($token->attributes() as $key => $value) {
            if ($key === $attribute) {
                return (bool) $value;
            }
        }
        return false;
    }

    /**
     * Checks if a given role is present in the token's role list.
     *
     * @param TokenInterface $token The token to check
     * @param string $role The role to check
     * @return bool Returns true if the role is present, otherwise false
     *
     * @template T of UserInterface
     * @phpstan-param TokenInterface<T> $token
     */
    public function checkRole(TokenInterface $token, string $role): bool
    {
        $roles = $token->roleNames();
        return in_array($role, $roles);
    }

    /**
     * Check if any attribute in the given list is granted.
     *
     * @param array<string> $attribute The list of attributes to check.
     *
     * @return bool Returns true if any attribute is granted, otherwise false.
     */
    public function checkAttributesList(array $attribute): bool
    {
        foreach ($attribute as $attr) {
            if ($this->isGranted($attr)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    
    public function authenticationErrors(): array
    {
        return $this->errors;
    }

    public function user(): ?UserInterface
    {
        $token = $this->tokenStorage->getToken();
        if ($token instanceof TokenInterface) {
            $user = $token->user();
            if ($user instanceof UserInterface) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Logs in the user using the provided UserInterface.
     *
     * @param UserInterface $user The user to log in.
     * @return void
     */
    public function login(UserInterface $user): void
    {
        if ($this->securityProfile instanceof StatefulSecurityProfileInterface) {
            $token = new UserToken($user);
            $token->withAttributes([
                'IS_AUTHENTICATED_FULLY' => 'true',
                'IS_AUTHENTICATED_REMEMBERED' => 'true',
                'IS_AUTHENTICATED' => 'true'
            ]);
            $this->securityProfile->login($token);
        }
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD)
     */
    public function logout(): void
    {
        $this->sessionDriver?->erase(SessionSecurityProfile::SESSION_KEY);

        $profile = $this->profileFactory->profile();
        if ($profile instanceof StatefulSecurityProfileInterface) {
            $profile->logout();
        }
        $_SESSION = [];
    }

    /**
     * @inheritDoc
     */
    public function processEntryPoint(ServerRequestInterface $request): ?ResponseInterface
    {
        return $this->securityProfile?->processEntryPoint($request);
    }
}
