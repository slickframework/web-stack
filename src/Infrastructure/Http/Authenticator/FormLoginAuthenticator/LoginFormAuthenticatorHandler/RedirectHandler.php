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
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Response;
use Slick\Http\Session\SessionDriverInterface;

/**
 * RedirectHandler
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler
 */
final class RedirectHandler implements AuthenticatorHandlerInterface
{

    public const LAST_URI = '_last_uri';

    private FormLoginProperties $properties;

    public function __construct(
        private readonly SessionDriverInterface $sessionDriver,
        ?FormLoginProperties                    $properties = null
    ) {
        $this->properties = $properties ?? new FormLoginProperties([]);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(ServerRequestInterface $request, TokenInterface $token): ResponseInterface
    {
        $location = $this->resolveRedirectLocation();
        return new Response(status: 302, headers: ['Location' => $location]);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(
        ServerRequestInterface $request,
        AuthenticationException $exception
    ): ?ResponseInterface {
        $path = $request->getUri()->getPath();
        $shouldRedirect = !in_array($path, $this->properties->paths());
        return $shouldRedirect
            ? new Response(status: 302, headers: ['Location' => $this->properties->path('login')])
            : null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticate(ServerRequestInterface $request, PassportInterface $passport): PassportInterface
    {
        return $passport;
    }

    /**
     * @return string
     */
    private function resolveRedirectLocation(): string
    {
        $sessionPath = (string) $this->sessionDriver->get(self::LAST_URI);
        $loginPaths = $this->properties->paths();
        $location = $this->properties->path('defaultTarget');

        return $sessionPath && !in_array($sessionPath, $loginPaths) ? $sessionPath : $location;
    }
}
