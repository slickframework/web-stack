<?php

namespace spec\Slick\WebStack\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Http\Server\Request;
use Slick\Http\Uri;
use Slick\WebStack\Http\UrlRewriterMiddleware;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UrlRewriterMiddlewareSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UrlRewriterMiddleware::class);
    }

    function its_an_http_middleware()
    {
        $this->shouldBeAnInstanceOf(MiddlewareInterface::class);
    }

    function it_cleans_the_url_query_parameter(
        ResponseInterface $response,
        ServerRequestInterface $request
    ) {
        $uri = new Uri('/?url=the/test&foo=bar');
        $request->getQueryParams()
            ->shouldBeCalled()
            ->willReturn(['url' => 'the/test', 'foo' => 'bar']);
        $request->getUri()
            ->shouldBeCalled()
            ->willReturn($uri);
        $request->withUri(Argument::that(function(UriInterface $uri) {
            return $uri->getPath() === '/the/test';
        }))
            ->shouldBeCalled()
            ->willReturn($request);
        $this->handle($request, $response)->shouldBe($response);

    }
}
