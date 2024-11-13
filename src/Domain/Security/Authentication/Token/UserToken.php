<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Authentication\Token;

use Slick\WebStack\Domain\Security\Authentication\Token\AbstractToken;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * UserToken
 *
 * @package Slick\WebStack\Domain\Security\Authentication\Token
 * @implements TokenInterface<UserInterface>
 */
final class UserToken extends AbstractToken implements TokenInterface
{

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
        parent::__construct($user->roles());
    }
}
