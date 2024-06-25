<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Csrf\CsrfToken;
use Slick\WebStack\Domain\Security\Csrf\CsrfTokenManagerInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\CsrfBadge;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * CsrfTokenHandler
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler
 */
final readonly class CsrfTokenHandler implements AuthenticatorHandlerInterface
{

    /**
     * Creates a CsrfTokenHandler
     *
     * @param FormLoginProperties $properties
     * @param CsrfTokenManagerInterface $tokenManager
     */
    public function __construct(
        private FormLoginProperties       $properties,
        private CsrfTokenManagerInterface $tokenManager
    ) {
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(ServerRequestInterface $request, TokenInterface $token): ?ResponseInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(
        ServerRequestInterface $request,
        AuthenticationException $exception
    ): ?ResponseInterface {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticate(ServerRequestInterface $request, PassportInterface $passport): PassportInterface
    {
        if (!$this->properties->enableCsrf()) {
            return $passport;
        }

        $tokenId = $this->properties->parameter('csrf') ?? '_csrf';
        $parsedBody = $request->getParsedBody();
        $loaded = $parsedBody[$tokenId] ?? null;
        $formToken = $loaded !== null ? new CsrfToken($tokenId, $loaded) : $this->tokenManager->tokenWithId($tokenId);

        $passport->addBadge(new CsrfBadge($formToken, fn() => $this->tokenManager->isTokenValid($formToken)));
        return $passport;
    }
}
