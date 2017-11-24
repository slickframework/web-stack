<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Http\Server;

use Aura\Router\Matcher;
use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Http\Server\RouterMiddleware;
use PhpSpec\ObjectBehavior;

/**
 * RouterMiddlewareSpec specs
 *
 * @package spec\Slick\WebStack\Http\Server
 */
class RouterMiddlewareSpec extends ObjectBehavior
{

    function let(
        RouterContainer $routerContainer,
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        ResponseInterface $response,
        Matcher $matcher
    )
    {
        $routerContainer->getMatcher()->willReturn($matcher);
        $handler->handle($request)->willReturn($response);
        $this->beConstructedWith($routerContainer);
    }

    function its_a_middleware()
    {
        $this->shouldBeAnInstanceOf(MiddlewareInterface::class);
    }

    function it_is_initializable_with_a_router_container()
    {
        $this->shouldHaveType(RouterMiddleware::class);
    }

    function it_matches_the_request_to_its_routes_container(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        ResponseInterface $response,
        Matcher $matcher,
        Route $route
    )
    {

        $matcher->match($request)->shouldBeCalled()->willReturn($route);

        $request->withAttribute('route', $route)->shouldBeCalled()->willReturn($request);

        $this->process($request, $handler)->shouldBe($response);
    }

    function it_returns_a_405_response_if_method_not_allowed(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        Matcher $matcher
    ) {
        $matcher->match($request)->willReturn(null);

        $failRoute = new Route();
        $failRoute->allows(['POST']);
        $failRoute->failedRule('Aura\Router\Rule\Allows');

        $matcher->getFailedRoute()->willReturn($failRoute);

        $response = $this->process($request, $handler);
        $response->getStatusCode()->shouldBe(405);
        $response->getHeaderLine('Allow')->shouldBe('POST');
    }

    function it_returns_a_406_response_if_request_not_accepted(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        Matcher $matcher
    ) {
        $matcher->match($request)->willReturn(null);

        $failRoute = new Route();
        $failRoute->failedRule('Aura\Router\Rule\Accepts');
        $matcher->getFailedRoute()->willReturn($failRoute);

        $response = $this->process($request, $handler);
        $response->getStatusCode()->shouldBe(406);
    }

    function it_returns_a_404_response_for_all_other_failed_routes(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        Matcher $matcher
    ) {
        $matcher->match($request)->willReturn(null);

        $failRoute = new Route();
        $failRoute->failedRule('other');
        $matcher->getFailedRoute()->willReturn($failRoute);

        $response = $this->process($request, $handler);
        $response->getStatusCode()->shouldBe(404);
    }
}