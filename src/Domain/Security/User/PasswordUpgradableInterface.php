<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\User;

use Slick\WebStack\Domain\Security\UserInterface;

/**
 * PasswordUpgradableInterface
 *
 * @package Slick\WebStack\Domain\Security\User
 */
interface PasswordUpgradableInterface extends UserInterface
{

    /**
     * Upgrades the hashed password.
     *
     * Because you don't want your users not being able to log in, this method should be opportunistic:
     *  it's fine if it does nothing or if it fails without throwing any exception.
     *
     * @param string $hashedPassword The hashed password to upgrade.
     * @return self
     */
    public function upgradePassword(string $hashedPassword): self;
}
