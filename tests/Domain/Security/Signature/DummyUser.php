<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Slick\WebStack\Domain\Security\Signature;

use Slick\WebStack\Domain\Security\UserInterface;
use DateTimeImmutable;

/**
 * DummyUser
 *
 * @package Test\Slick\WebStack\Domain\Security\Signature
 */
final class DummyUser implements UserInterface
{

    private string $name;
    private DateTimeImmutable $registeredOn;
    private object $email;

    private int $userId = 982;

    public function __construct()
    {
        $this->name = "John Doe";
        $this->registeredOn = new DateTimeImmutable();
        $this->email = (object) ['value' => "john.doe@example.com"];
    }

    /**
     * @inheritDoc
     */
    public function userIdentifier(): string
    {
        return 'userIdentifier';
    }

    /**
     * DummyUser name
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * DummyUser registeredOn
     *
     * @return DateTimeImmutable
     */
    public function registeredOn(): DateTimeImmutable
    {
        return $this->registeredOn;
    }

    /**
     * DummyUser email
     *
     * @return object
     */
    public function email(): object
    {
        return $this->email;
    }

    /**
     * DummyUser userId
     *
     * @return int
     */
    public function userId(): int
    {
        return $this->userId;
    }

    /**
     * @inheritDoc
     */
    public function roles(): array
    {
        return ["ROLE_USER"];
    }
}
