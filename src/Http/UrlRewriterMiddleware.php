<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\WebStack\Http\UrlRewriter\Uri;

/**
 * UrlRewriterMiddleware
 *
 * @package Slick\WebStack\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class UrlRewriterMiddleware extends AbstractMiddleware implements
    MiddlewareInterface
{
    /**
     * Handles a Request and updated the response
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function handle(
        ServerRequestInterface $request, ResponseInterface $response
    )
    {
        $queryParams = $request->getQueryParams();
        $path = array_key_exists('url', $queryParams)
            ? $queryParams['url']
            : '';
        $uri = new Uri((string) $request->getUri());
        $queryString = ltrim(
            str_replace("url={$path}", '', $uri->getQuery()),
            '&'
        );
        $uri = $uri->withPath(str_replace('//', '/', "/$path"))
            ->withQuery($queryString);
        $request = $request->withUri($uri);
        return $this->executeNext($request, $response);
    }
}