<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;

/**
 * RoutingMiddleware
 *
 * @package Slick\WebStack\Infrastructure\Http
 */
final class RoutingMiddleware implements MiddlewareInterface
{

    /**
     * Creates a RoutingMiddleware
     *
     * @param UrlMatcherInterface $matcher
     */
    public function __construct(
        private UrlMatcherInterface $matcher
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parameters = $this->matcher->match($request->getUri()->getPath());
        $request = $request->withAttribute('route', $parameters);
        return $handler->handle($request);
    }
}
