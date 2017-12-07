<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Http\Server;

use Aura\Router\Route;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Controller\ContextCreator;
use Slick\WebStack\Controller\ControllerContextInterface;
use Slick\WebStack\Dispatcher\ControllerDispatch;
use Slick\WebStack\Dispatcher\ControllerDispatchInflectorInterface;
use Slick\WebStack\Dispatcher\ControllerInvokerInterface;
use Slick\WebStack\Exception\BadHttpStackConfigurationException;
use Slick\WebStack\Exception\MissingResponseException;
use Slick\WebStack\Http\Server\DispatcherMiddleware;
use PhpSpec\ObjectBehavior;

/**
 * DispatcherMiddlewareSpec specs
 *
 * @package spec\Slick\WebStack\Http\Server
 */
class DispatcherMiddlewareSpec extends ObjectBehavior
{

    private $controllerData = ['foo' => 'bar'];
    private $assignData = ['bar' => 'baz', 'foo' => 'bar'];

    function let(
        ServerRequestInterface $request,
        Route $route,
        RequestHandlerInterface $handler,
        ResponseInterface $response,
        ControllerDispatchInflectorInterface $inflector,
        ControllerInvokerInterface $invoker,
        ControllerDispatch $controllerDispatch,
        ContextCreator $contextCreator,
        ControllerContextInterface $context
    )
    {
        $request->getAttribute('route', false)
            ->willReturn($route);
        $request->withAttribute('viewData', Argument::any())->willReturn($request);
        $request->getAttribute('viewData', [])->willReturn(['bar' => 'baz']);
        $handler->handle($request)->willReturn($response);

        $inflector->inflect($route)->willReturn($controllerDispatch);

        $contextCreator->create($request, $route)->willReturn($context);
        $context->handlesResponse()->willReturn(false);

        $invoker->invoke($context, $controllerDispatch)
            ->willReturn($this->controllerData);

        $this->beConstructedWith($inflector, $invoker, $contextCreator);
    }

    function its_an_http_middleware()
    {
        $this->shouldBeAnInstanceOf(MiddlewareInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DispatcherMiddleware::class);
    }

    function it_process_the_request_if_it_has_a_route(
        ServerRequestInterface $request,
        ResponseInterface $response,
        ControllerDispatchInflectorInterface $inflector,
        Route $route,
        ControllerInvokerInterface $invoker,
        ControllerDispatch $controllerDispatch,
        RequestHandlerInterface $handler,
        ControllerContextInterface $context
    )
    {
        $this->process($request, $handler)->shouldBe($response);
        $inflector->inflect($route)->shouldHaveBeenCalled();
        $invoker->invoke(
            $context,
            $controllerDispatch
        )
            ->shouldHaveBeenCalled();
    }

    function it_throws_an_exception_for_missing_route_in_request(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    )
    {
        $request->getAttribute('route', false)
            ->willReturn(false);
        $this->shouldThrow(BadHttpStackConfigurationException::class)
            ->during('process', [$request, $handler]);
    }

    function it_stores_the_dispatch_data_into_request_for_next_handler(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    )
    {
        $request->withAttribute('viewData', $this->assignData)
            ->shouldBeCalled()
            ->willReturn($request);
        $this->process($request, $handler);
    }

    function it_returns_the_context_response_when_available(
        ServerRequestInterface $request,
        ResponseInterface $controlledResponse,
        RequestHandlerInterface $handler,
        ControllerContextInterface $context
    )
    {
        $context->handlesResponse()->willReturn(true);
        $context->response()->shouldBeCalled()->willReturn($controlledResponse);
        $this->process($request, $handler)->shouldBe($controlledResponse);
    }

    function it_throws_an_exception_if_render_is_disabled_and_no_response_is_given(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        ControllerContextInterface $context
    )
    {
        $context->handlesResponse()->willReturn(true);
        $context->response()->shouldBeCalled()->willReturn(null);
        $this->shouldThrow(MissingResponseException::class)
            ->during('process', [$request, $handler]);
    }
}