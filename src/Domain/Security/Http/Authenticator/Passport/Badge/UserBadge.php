<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge;

use Slick\WebStack\Domain\Security\Exception\AuthenticationServiceException;
use Slick\WebStack\Domain\Security\Exception\BadCredentialsException;
use Slick\WebStack\Domain\Security\Exception\UserNotFoundException;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\BadgeInterface;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * UserBadge
 *
 * @package Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge
 * @template-covariant TUser of UserInterface
 */
class UserBadge implements BadgeInterface
{
    public const MAX_USERNAME_LENGTH = 4096;

    private ?UserInterface $user = null;

    /** @var callable|UserProviderInterface<TUser> */
    private readonly mixed $provider;

    /**
     * Creates a UserBadge
     *
     * @param string $userIdentifier
     * @param UserProviderInterface<TUser>|callable $provider
     * @throws BadCredentialsException
     */
    public function __construct(
        private readonly string $userIdentifier,
        UserProviderInterface|callable $provider
    ) {
        if (strlen($this->userIdentifier) > self::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Username too long.');
        }
        $this->provider = $provider;
    }

    /**
     * UserBadge user
     *
     * @return UserInterface
     * @throws SecurityException
     */
    public function user(): UserInterface
    {
        if (null === $this->user) {
            $this->user = $this->retrieveUser();
        }
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function isResolved(): bool
    {
        return true;
    }

    /**
     * UserBadge userIdentifier
     *
     * @return string
     */
    public function userIdentifier(): string
    {
        return $this->userIdentifier;
    }

    /**
     * UserBadge provider
     *
     * @return callable|UserProviderInterface<TUser>
     */
    public function provider(): callable|UserProviderInterface
    {
        return $this->provider;
    }

    /**
     * @throws SecurityException|UserNotFoundException
     * @throws SecurityException|AuthenticationServiceException
     */
    private function retrieveUser(): UserInterface
    {
        if ($this->provider instanceof UserProviderInterface) {
            return $this->provider->loadUserByIdentifier($this->userIdentifier);
        }

        /** @phpstan-var TUser $user */
        $user = ($this->provider)($this->userIdentifier);
        if (null === $user) {
            throw new UserNotFoundException(sprintf('User with identifier "%s" not found.', $this->userIdentifier));
        }

        if (!$user instanceof UserInterface) {
            throw new AuthenticationServiceException(
                sprintf('The user provider must return a UserInterface object, "%s" given.', get_debug_type($user))
            );
        }

        return $user;
    }
}
