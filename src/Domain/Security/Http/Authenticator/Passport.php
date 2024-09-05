<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\Authenticator;

use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials\PasswordCredentials;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\CredentialsInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\BadgeInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * Passport
 *
 * @package Slick\WebStack\Domain\Security\Http\Authenticator
 * @template-covariant  TUser of UserInterface
 * @implements PassportInterface<TUser>
 */
class Passport implements PassportInterface
{
    use PassportMethodsTrait;

    /**
     * Creates a Passport
     *
     * @param UserBadge<TUser> $userBadge
     * @param CredentialsInterface $credentials
     * @param array<BadgeInterface> $badges
     */
    public function __construct(UserBadge $userBadge, CredentialsInterface $credentials, array $badges = [])
    {
        $this->addBadge($userBadge, UserBadge::class);
        $this->addBadge($credentials, get_class($credentials));
        foreach ($badges as $badge) {
            $this->addBadge($badge);
        }
    }
}
