<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator;

use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * HandlerLIst
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler
 */
final class FormLoginHandler implements AuthenticatorHandlerInterface
{

    /**
     * @phpstan-var array<AuthenticatorHandlerInterface>
     */
    private array $handlers = [];

    /**
     * Creates a HandlerLIst
     *
     * @param array<AuthenticatorHandlerInterface> $handlers
     */
    public function __construct(array $handlers = [])
    {
        foreach ($handlers as $handler) {
            $this->add($handler);
        }
    }

    public function add(AuthenticatorHandlerInterface $handler): self
    {
        $this->handlers[] = $handler;
        return $this;
    }


    /**
     * @inheritDoc
     * @template TUser of UserInterface
     * @phpstan-param TokenInterface<TUser> $token
     */
    public function onAuthenticationSuccess(ServerRequestInterface $request, TokenInterface $token): ?ResponseInterface
    {
       foreach ($this->handlers as $handler) {
           $response = $handler->onAuthenticationSuccess($request, $token);
           if ($response instanceof ResponseInterface) {
               return $response;
           }
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
        foreach ($this->handlers as $handler) {
            $response = $handler->onAuthenticationFailure($request, $exception);
            if ($response instanceof ResponseInterface) {
                return $response;
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
        public function onAuthenticate(ServerRequestInterface $request, PassportInterface $passport): PassportInterface
    {
        foreach ($this->handlers as $handler) {
            $passport = $handler->onAuthenticate($request, $passport);
        }
        return $passport;
    }
}
