<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials;

use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\CredentialsInterface;
use SensitiveParameter;

/**
 * PasswordCredentials
 *
 * @package Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials
 */
class PasswordCredentials implements CredentialsInterface
{

    private bool $resolved = false;
    private ?string $password;

    public function __construct(#[SensitiveParameter] string $password)
    {
        $this->password = $password;
    }

    /**
     * PasswordCredentials password
     *
     * @return string
     */
    public function password(): string
    {
        if (null === $this->password) {
            throw new LogicException('The credentials are erased as another listener already verified these credentials.');
        }
        return $this->password;
    }

    /**
     * @inheritDoc
     */
    public function isResolved(): bool
    {
        return $this->resolved;
    }

    /**
     * Marks the credentials badge as resolved.
     *
     * @return void
     */
    public function markResolved(): void
    {
        $this->password = null;
        $this->resolved = true;
    }
}
