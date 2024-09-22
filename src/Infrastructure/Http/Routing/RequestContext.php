<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Routing;

use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Routing\RequestContext as SymfonyRequestContext;

/**
 * RequestContext
 *
 * @package Slick\WebStack\Infrastructure\Http\Routing
 */
final class RequestContext extends SymfonyRequestContext
{

    /**
     * Create a RequestContext object from a PSR-7 ServerRequestInterface object.
     *
     * @param ServerRequestInterface $request The PSR-7 ServerRequestInterface object.
     * @param string $baseUrl The base URL for the request context (optional).
     *
     * @return RequestContext The created RequestContext object.
     */
    public function fromPsrRequest(ServerRequestInterface $request, string $baseUrl = ''): RequestContext
    {
        $requestUri = $request->getUri() ;
        $isSecure = strtoupper($requestUri->getScheme()) === 'HTTPS';
        $httpPort = (int) $requestUri->getPort();

        return new RequestContext(
            $baseUrl,
            $request->getMethod(),
            $requestUri->getHost(),
            $requestUri->getScheme(),
            $httpPort,
            $isSecure && $httpPort > 0 ? $httpPort : 443,
            $requestUri->getPath(),
            (string) $requestUri->getQuery()
        );
    }
}
