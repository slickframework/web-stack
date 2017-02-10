<?php

namespace spec\Slick\WebStack\Http;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Http\SessionDriverInterface;
use Slick\WebStack\Http\SessionMiddleware;

class SessionMiddlewareSpec extends ObjectBehavior
{
    function let(SessionDriverInterface $sessionDriver)
    {
        $this->beConstructedWith($sessionDriver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SessionMiddleware::class);
    }

    function its_an_http_middleware()
    {
        $this->shouldBeAnInstanceOf(MiddlewareInterface::class);
    }

    function it_initializes_the_session_driver(
        ServerRequestInterface $request,
        ResponseInterface $response,
        SessionDriverInterface $sessionDriver
    )
    {
        $request->withAttribute('session', $sessionDriver)->willReturn($request);
        $this->handle($request, $response)->shouldBe($response);
        $request->withAttribute('session', $sessionDriver)->shouldHaveBeenCalled();
    }
}
