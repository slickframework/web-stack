<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Csrf\TokenStorage;

use Slick\WebStack\Domain\Security\Csrf\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Exception\CsrfTokenNotFound;
use SensitiveParameter;
use Slick\Http\Session\SessionDriverInterface;

/**
 * SessionCsrfTokenStorage
 *
 * @package Slick\WebStack\Domain\Security\Csrf\TokenStorage
 */
final class SessionCsrfTokenStorage implements TokenStorageInterface
{
    /**
     * The namespace used to store values in the session.
     */
    public const SESSION_NAMESPACE = '_csrf';

    /**
     * Creates a SessionCsrfTokenStorage
     *
     * @param SessionDriverInterface $session
     * @param string $namespace
     */
    public function __construct(
        private readonly SessionDriverInterface $session,
        private readonly string $namespace = self::SESSION_NAMESPACE
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get(string $tokenId): string
    {
        $existing = $this->session->get($this->namespace."_$tokenId");
        if (!$existing) {
            throw new CsrfTokenNotFound('The CSRF token with ID '.$tokenId.' does not exist.');
        }
        return $existing;
    }

    /**
     * @inheritDoc
     */
    public function set(string $tokenId, #[SensitiveParameter] string $token): void
    {
        $this->session->set($this->namespace."_$tokenId", $token);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $tokenId): void
    {
        $this->session->erase($this->namespace."_$tokenId");
    }

    /**
     * @inheritDoc
     */
    public function has(string $tokenId): bool
    {
        return null !== $this->session->get($this->namespace."_$tokenId");
    }
}
