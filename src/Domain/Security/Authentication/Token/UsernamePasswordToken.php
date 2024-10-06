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
use Slick\WebStack\Domain\Security\Common\AttributesBagMethods;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * UsernamePasswordToken
 *
 * @package Slick\WebStack\Domain\Security\Authentication\Token
 *
 * @implements  TokenInterface<UserInterface>
 */
final class UsernamePasswordToken extends AbstractToken implements TokenInterface
{

    /**
     * Class constructor.
     *
     * @param UserInterface $user The user object.
     * @param array<string, mixed> $roles The user's roles.
     */
    public function __construct(UserInterface $user, array $roles = [])
    {
        parent::__construct($roles);
        $this->user = $user;
    }
}
