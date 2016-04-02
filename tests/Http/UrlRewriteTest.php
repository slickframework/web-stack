<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Http;

use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Http\Uri;
use Slick\Mvc\Http\UrlRewrite;

/**
 * UrlRewrite Test Case
 *
 * @package Slick\Tests\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class UrlRewriteTest extends TestCase
{
    /**
     * Should use the URI fix and remove the query parameter used
     * in URL rewrite process
     * @test
     */
    public function handleRequest()
    {
        /** @var ResponseInterface $response */
        $response = $this->getMock(ResponseInterface::class);
        $middleware = new UrlRewrite();
        $middleware->setNext(new MiddlewareMock());
        $_GET['url'] = '/blog/2/edit';
        $request = new Request();
        $request = $request->withUri(
            new Uri('http://example.com/?url=/blog/2/edit')
        );
        $middleware->handle($request, $response);
    }
}

/**
 * MiddlewareMock
 *
 * @package Tests\Libs\Http
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class MiddlewareMock extends TestCase implements MiddlewareInterface
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
        $this->assertEquals('/blog/2/edit', $request->getUri()->getPath());
    }

    /**
     * The next middleware executed in the request handle process
     *
     * @param MiddlewareInterface $middleware
     *
     * @return self|$this|MiddlewareInterface
     */
    public function setNext(MiddlewareInterface $middleware = null)
    {
        return $this;
    }
}
