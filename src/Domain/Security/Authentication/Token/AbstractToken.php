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
 * AbstractToken
 *
 * @package Slick\WebStack\Domain\Security\Authentication\Token
 * @implements TokenInterface<UserInterface>
 */
abstract class AbstractToken implements TokenInterface
{
    use AttributesBagMethods;

    /** @var array<string> */
    protected array $roleNames = [];

    /** @phpstan-var UserInterface|null  */
    protected ?UserInterface $user = null;

    /**
     * Creates an AbstractToken
     *
     * @param array<string|\Stringable> $roles
     */
    public function __construct(array $roles = [])
    {
        foreach ($roles as $role) {
            $this->roleNames[] = (string) $role;
        }
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $class = static::class;
        $class = substr($class, strrpos($class, '\\') + 1);
        return sprintf(
            '%s(user="%s", roles="%s")',
            $class,
            $this->userIdentifier(),
            implode(', ', $this->roleNames)
        );
    }

    /**
     * @inheritDoc
     */
    public function userIdentifier(): string
    {
        return $this->user ? $this->user->userIdentifier() : '';
    }

    /**
     * @inheritDoc
     */
    public function roleNames(): array
    {
        return $this->roleNames;
    }

    /**
     * @inheritDoc
     */
    public function user(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @inheritDoc
     */
    public function __serialize(): array
    {
        return [
            "user" => $this->user,
            "roleNames" => $this->roleNames,
            "attributes" => $this->attributes
        ];
    }

    /**
     * @inheritDoc
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        $this->user = $data["user"];
        $this->roleNames = $data["roleNames"];
        $this->attributes = $data["attributes"];
    }
}
