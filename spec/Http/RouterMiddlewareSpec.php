<?php

namespace spec\Slick\WebStack\Http;

use Aura\Router\Matcher;
use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\MiddlewareInterface;
use Slick\WebStack\Http\RouterMiddleware;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RouterMiddlewareSpec extends ObjectBehavior
{
    function let(RouterContainer $routerContainer)
    {
        $this->beConstructedWith($routerContainer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RouterMiddleware::class);
    }

    function its_an_http_middleware()
    {
        $this->shouldBeAnInstanceOf(MiddlewareInterface::class);
    }

    function it_matches_the_request_to_its_routes_container(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouterContainer $routerContainer,
        Matcher $matcher,
        Route $route
    )
    {
        $routerContainer->getMatcher()->willReturn($matcher);
        $matcher->match($request)->willReturn($route);
        $request->withAttribute('route', $route)->willReturn($request);
        $this->beConstructedWith($routerContainer);
        $matcher->match($request)->shouldBeCalled();
        $this->handle($request, $response)->shouldBe($response);
        $request->withAttribute('route', $route)->shouldHaveBeenCalled();
    }

    function it_returns_a_405_response_if_method_not_allowed(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouterContainer $routerContainer,
        Matcher $matcher
    ) {
        $routerContainer->getMatcher()->willReturn($matcher);
        $matcher->match($request)->willReturn(null);
        $failRoute = new Route();
        $failRoute->allows(['POST']);
        $failRoute->failedRule('Aura\Router\Rule\Allows');
        $matcher->getFailedRoute()->willReturn($failRoute);

        $request->withAttribute('route', null)
            ->willReturn($request);

        $response->withStatus(405)
            ->shouldBeCalled()
            ->willReturn($response);

        $response->withHeader('allow', $failRoute->allows)
            ->shouldBeCalled()
            ->willReturn($response);

        $this->beConstructedWith($routerContainer);
        $this->handle($request, $response)->shouldBe($response);
    }

    function it_returns_a_406_response_if_request_not_accepted(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouterContainer $routerContainer,
        Matcher $matcher
    ) {
        $routerContainer->getMatcher()->willReturn($matcher);
        $matcher->match($request)->willReturn(null);
        $failRoute = new Route();
        $failRoute->failedRule('Aura\Router\Rule\Accepts');
        $matcher->getFailedRoute()->willReturn($failRoute);

        $request->withAttribute('route', null)
            ->willReturn($request);

        $response->withStatus(406)
            ->shouldBeCalled()
            ->willReturn($response);

        $this->beConstructedWith($routerContainer);
        $this->handle($request, $response)->shouldBe($response);
    }

    function it_returns_a_404_response_for_all_other_failed_routes(
        ServerRequestInterface $request,
        ResponseInterface $response,
        RouterContainer $routerContainer,
        Matcher $matcher
    ) {
        $routerContainer->getMatcher()->willReturn($matcher);
        $matcher->match($request)->willReturn(null);
        $failRoute = new Route();
        $failRoute->failedRule('other');
        $matcher->getFailedRoute()->willReturn($failRoute);

        $request->withAttribute('route', null)
            ->willReturn($request);

        $response->withStatus(404)
            ->shouldBeCalled()
            ->willReturn($response);

        $this->beConstructedWith($routerContainer);
        $this->handle($request, $response)->shouldBe($response);
    }
}
