<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\SecurityProfile;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfileInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * StatefulSecurityProfileInterface
 *
 * @package Slick\WebStack\Domain\Security\Http\SecurityProfile
 * @template-covariant TUser of UserInterface
 */
interface StatefulSecurityProfileInterface extends SecurityProfileInterface
{

    /**
     * Restores the token.
     *
     * @return TokenInterface<TUser>|null The restored token, or null if nothing was restored.
     */
    public function restoreToken(): ?TokenInterface;
}
