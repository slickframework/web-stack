<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Slick\WebStack\Domain\Security\Authentication\Token;

use Slick\WebStack\Domain\Security\UserInterface;
use Override;

/**
 * SimpleUser
 *
 * @package Test\Slick\WebStack\Domain\Security\Authentication\Token
 */
final class TestUser implements UserInterface
{

    /**
     * @inheritDoc
     */
    public function userIdentifier(): string
    {
        return 'Some-test-ID';
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function roles(): array
    {
        return ['ROLE_USER'];
    }
}
