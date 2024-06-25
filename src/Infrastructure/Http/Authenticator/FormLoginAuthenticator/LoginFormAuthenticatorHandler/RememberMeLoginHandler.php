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
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeHandlerInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * RememberMeLoginHandler
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler
 */
final readonly class RememberMeLoginHandler implements AuthenticatorHandlerInterface
{

    /**
     * Creates a RememberMeLoginHandler
     *
     * @param FormLoginProperties $properties
     * @param RememberMeHandlerInterface $handler
     */
    public function __construct(
        private FormLoginProperties        $properties,
        private RememberMeHandlerInterface $handler
    ) {
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(ServerRequestInterface $request, TokenInterface $token): ?ResponseInterface
    {
        $user = $token->user();
        if ($this->shouldRememberMe($request) && $user instanceof UserInterface) {
            $this->handler->createRememberMeCookie($user);
        }
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
        return $passport;
    }

    /**
     * Checks if the "remember me" feature should be enabled for the given request.
     *
     * @param ServerRequestInterface $request The request object.
     *
     * @return bool Returns true if "remember me" should be enabled, false otherwise.
     */
    private function shouldRememberMe(ServerRequestInterface $request): bool
    {
        $parsedBody = $request->getParsedBody();
        if (!is_array($parsedBody)) {
            return false;
        }

        $rememberMeField = $this->properties->parameter('rememberMe');
        return$this->properties->rememberMe() &&
            array_key_exists((string)$rememberMeField, $parsedBody) &&
            $parsedBody[$rememberMeField];
    }
}
