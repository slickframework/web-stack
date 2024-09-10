<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\Authenticator;

use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\BadgeInterface;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * PassportMethodsTrait
 *
 * @package Slick\WebStack\Domain\Security\Http\Authenticator
 */
trait PassportMethodsTrait
{

    protected ?UserInterface $user = null;

    /** @var array<BadgeInterface>  */
    protected array $badges = [];

    /**
     * The user that will be or was subject of authentication
     *
     * @return UserInterface
     * @throws SecurityException
     */
    public function user(): UserInterface
    {
        if (null === $this->user) {
            /** @phpstan-var UserBadge<UserInterface> $badge */
            $badge = $this->badge(UserBadge::class);
            $this->user = $badge->user();
        }

        return $this->user;
    }

    /**
     * Adds a new security badge.
     *
     *  A passport can hold only one instance of the same security badge.
     *  This method replaces the current badge if it is already set on this
     *  passport.
     *
     * @param BadgeInterface $badge
     * @param string|null $badgeName A FQCN to which the badge should be mapped to.
     *                                This allows replacing a built-in badge by a custom one using
     *                                e.g. addBadge(new MyCustomUserBadge(), UserBadge::class)
     *
     * @return static
     */
    public function addBadge(BadgeInterface $badge, ?string $badgeName = null): static
    {
        $badgeName ??= $badge::class;
        $this->badges[$badgeName] = $badge;
        return $this;
    }

    /**
     * Check if a badge exists.
     *
     * @param string $badgeName The name of the badge to check.
     *
     * @return bool Returns true if the badge exists, false otherwise.
     */
    public function hasBadge(string $badgeName): bool
    {
        return array_key_exists($badgeName, $this->badges);
    }

    /**
     * Returns the badge with the given name.
     *
     * @param string $badgeName The name of the badge.
     * @return BadgeInterface|null The BadgeInterface instance if found; otherwise null.
     */
    public function badge(string $badgeName): ?BadgeInterface
    {
        return $this->badges[$badgeName] ?? null;
    }

    /**
     * @return array<class-string<BadgeInterface>, BadgeInterface>
     */
    public function badges(): array
    {
        return $this->badges;
    }
}
