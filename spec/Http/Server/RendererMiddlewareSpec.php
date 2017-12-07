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
use PhpSpec\Exception\Example\FailureException;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Http\Server\RendererMiddleware;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Renderer\ViewInflectorInterface;

/**
 * RendererMiddlewareSpec specs
 *
 * @package spec\Slick\WebStack\Http\Server
 */
class RendererMiddlewareSpec extends ObjectBehavior
{
    private $data = ['foo', 'bar'];

    function let(
        TemplateEngineInterface $templateEngine,
        ViewInflectorInterface $viewInflector,
        Route $route,
        ServerRequestInterface $request,
        ResponseInterface $response,
        RequestHandlerInterface $handler
    )
    {
        $request->getAttribute('route')->willReturn($route);
        $request->getAttribute('viewData', [])->willReturn($this->data);
        $handler->handle($request)->willReturn($response);
        $this->beConstructedWith($templateEngine, $viewInflector);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RendererMiddleware::class);
    }

    function its_a_server_middleware()
    {
        $this->shouldBeAnInstanceOf(MiddlewareInterface::class);
    }

    function it_handles_the_request_by_populating_the_response_body(
        TemplateEngineInterface $templateEngine,
        ViewInflectorInterface $viewInflector,
        Route $route,
        ResponseInterface $response,
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    )
    {
        $template = 'som/twig.twig';
        $viewInflector->inflect($route)->shouldBeCalled()->willReturn($template);
        $templateEngine->parse($template)
            ->shouldBeCalled()
            ->willReturn($templateEngine);
        $templateEngine->process($this->data)
            ->shouldBeCalled()
            ->willReturn('test');
        $response->withBody(Argument::type(StreamInterface::class))
            ->shouldBeCalled()
            ->willReturn($response);
        $this->process($request, $handler)->shouldBe($response);
    }
}