<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\Authenticator;

use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\BadgeInterface;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * PassportInterface
 *
 * @package Slick\WebStack\Domain\Security\Http\Authenticator
 * @template-covariant TUser of UserInterface
 */
interface PassportInterface
{

    /**
     * The user that will be or was subject of authentication
     *
     * @return TUser|UserInterface
     * @throws SecurityException
     */
    public function user(): UserInterface;

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
    public function addBadge(BadgeInterface $badge, ?string $badgeName = null): static;

    /**
     * Check if a badge exists.
     *
     * @param string $badgeName The name of the badge to check.
     *
     * @return bool Returns true if the badge exists, false otherwise.
     */
    public function hasBadge(string $badgeName): bool;

    /**
     * Returns the badge with the given name.
     *
     * @param string $badgeName The name of the badge.
     * @return BadgeInterface|null The BadgeInterface instance if found; otherwise null.
     */
    public function badge(string $badgeName): ?BadgeInterface;

    /**
     * @return array<class-string<BadgeInterface>, BadgeInterface>
     */
    public function badges(): array;
}
