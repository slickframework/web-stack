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
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * SelfValidatingPassport
 *
 * @package Slick\WebStack\Domain\Security\Http\Authenticator
 *
 * @template-covariant  TUser of UserInterface
 * @implements PassportInterface<TUser>
 */
final class SelfValidatingPassport implements PassportInterface
{
    use PassportMethodsTrait;

    /**
     * An implementation used when there are no credentials to be checked (e.g.
     *  API token authentication).
     *
     * @param UserBadge<TUser> $userBadge The main user badge.
     * @param array<BadgeInterface> $badges An array of additional badges.
     */
    public function __construct(UserBadge $userBadge, array $badges = [])
    {
        $this->addBadge($userBadge, UserBadge::class);
        foreach ($badges as $badge) {
            $this->addBadge($badge);
        }
    }
}
