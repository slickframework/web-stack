<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;

/**
 * UrlRewrite Middleware
 *
 * @package Slick\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class UrlRewrite extends AbstractMiddleware implements MiddlewareInterface
{

    /**
     * The query param used in the URL rewrite process
     */
    const QUERY_PARAM = 'url';

    /**
     * Handles a Request and updated the response
     *
     * @param RequestInterface|ServerRequestInterface|Request $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function handle(
        ServerRequestInterface $request, ResponseInterface $response
    )
    {
        $path = array_key_exists(self::QUERY_PARAM, $request->getQueryParams())
            ? $request->getQueryParams()[self::QUERY_PARAM]
            : '';
        $uri = Uri::create($request->getUri());
        $queryString = ltrim(
            str_replace(
                self::QUERY_PARAM."={$path}",
                '',
                $uri->getQuery()
            ),
            '&'
        );
        $uri = $uri->withPath(str_replace('//', '/', "/$path"))
            ->withQuery($queryString);
        /** @var ServerRequestInterface $request */
        $request = $request->withUri($uri);
        return $this->executeNext($request, $response);
    }
}