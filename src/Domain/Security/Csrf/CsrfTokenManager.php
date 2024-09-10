<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Csrf;

/**
 * CsrfTokenManager
 *
 * @package Slick\WebStack\Domain\Security\Csrf
 */
final class CsrfTokenManager implements CsrfTokenManagerInterface
{

    public function __construct(
        private readonly TokenStorageInterface $storage,
        private readonly TokenGeneratorInterface $generator
    ) {
    }

    /**
     * @inheritDoc
     */
    public function tokenWithId(string $tokenId): CsrfToken
    {
        if ($this->storage->has($tokenId)) {
            return new CsrfToken($tokenId, $this->storage->get($tokenId));
        }

        return $this->refreshToken($tokenId);
    }

    /**
     * @inheritDoc
     */
    public function refreshToken(string $tokenId): CsrfToken
    {
        $token = new CsrfToken($tokenId, $this->generator->generateToken());
        $this->storage->set($tokenId, $token->value());
        return $token;
    }

    /**
     * @inheritDoc
     */
    public function removeToken(string $tokenId): void
    {
        if ($this->storage->has($tokenId)) {
            $this->storage->remove($tokenId);
        }
    }

    /**
     * @inheritDoc
     */
    public function isTokenValid(CsrfToken $token): bool
    {
        if (!$this->storage->has($token->tokenId())) {
            return false;
        }

        return $token->value() === $this->storage->get($token->tokenId());
    }
}
