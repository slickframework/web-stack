<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Authentication\Token;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * TokenValidatorInterface
 *
 * @package Slick\WebStack\Domain\Security\Authentication\Token
 */
interface TokenValidatorInterface
{

    /**
     * Validates the given token.
     *
     * @param TokenInterface<UserInterface> $token The token to be validated
     * @return bool Returns true if the token is valid, false otherwise
     */
    public function validate(TokenInterface $token): bool;
}
