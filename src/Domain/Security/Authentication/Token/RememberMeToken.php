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
use SensitiveParameter;

/**
 * RememberMeToken
 *
 * @package Slick\WebStack\Domain\Security\Authentication\Token
 *
 * @implements TokenInterface<UserInterface>
 */
final class RememberMeToken extends AbstractToken implements TokenInterface
{

    /**
     * Creates a RememberMeToken
     *
     * @param UserInterface $user
     * @phpstan-param UserInterface $user
     * @param string $secret
     */
    public function __construct(
        UserInterface $user,
        #[SensitiveParameter]
        private string $secret
    ) {
        $this->user = $user;
        $roles = $this->user()?->roles();
        parent::__construct($roles ?? []);
    }

    /**
     * RememberMeToken secret
     *
     * @return string
     */
    public function secret(): string
    {
        return $this->secret;
    }

    /**
     * @inheritDoc
     */
    public function __serialize(): array
    {
        return [$this->secret, parent::__serialize()];
    }

    /**
     * @inheritDoc
     * @param array<string|int, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        [$this->secret, $parentData] = $data;
        $parentData = is_array($parentData) ? $parentData : unserialize($parentData);
        parent::__unserialize($parentData);
    }
}
