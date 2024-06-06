<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http;

use Slick\WebStack\Domain\Security\AuthorizationCheckerInterface;
use Slick\WebStack\Domain\Security\SecurityAuthenticatorInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slick\Http\Message\Response;

/**
 * SecurityMiddleware
 *
 * @package Slick\WebStack\Infrastructure\Http
 */
final readonly class SecurityMiddleware implements MiddlewareInterface
{

    /**
     * Creates a SecurityMiddleware
     *
     * @template T of UserInterface
     *
     * @param SecurityAuthenticatorInterface $security
     * @param AuthorizationCheckerInterface<T> $authorizationChecker
     */
    public function __construct(
        private SecurityAuthenticatorInterface $security,
        private AuthorizationCheckerInterface  $authorizationChecker
    ) {

    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $this->checkAcl($request, $this->security->process($request));

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        return $handler->handle($request);
    }

    /**
     * Checks the ACL for the given request and returns the response if granted, or a 403 Response otherwise.
     *
     * @param ServerRequestInterface $request The request to check for ACL permissions
     * @param ResponseInterface|null $response The response to return if ACL is granted
     * @return ResponseInterface|null The response if ACL is granted, or a 403 Response otherwise
     */
    private function checkAcl(ServerRequestInterface $request, ?ResponseInterface $response = null): ?ResponseInterface
    {
        if ($this->authorizationChecker->isGrantedAcl($request)) {
            return $response;
        }

        return new Response(403,'Access denied.');
    }
}
