<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\AccessToken;

use SensitiveParameter;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * AccessTokenHandlerInterface
 *
 * @package Slick\WebStack\Domain\Security\Http\AccessToken
 */
interface AccessTokenHandlerInterface
{

    /**
     * Retrieves a UserBadge based on the provided access token.
     *
     * @param string $accessToken #[SensitiveParameter] The access token used to fetch the UserBadge
     * @return UserBadge<UserInterface> The UserBadge associated with the provided access token
     */
    public function userBadgeFromToken(#[SensitiveParameter] string $accessToken): UserBadge;
}
