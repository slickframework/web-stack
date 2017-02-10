<?php

namespace spec\Slick\WebStack\Http;

use Aura\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Http\Stream;
use Slick\WebStack\Http\Renderer\ViewInflectorInterface;
use Slick\WebStack\Http\RendererMiddleware;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\Template\Template;
use Slick\Template\TemplateEngineInterface;

/**
 * RendererMiddlewareSpec
 *
 * @package spec\Slick\WebStack\Http
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class RendererMiddlewareSpec extends ObjectBehavior
{

    function let(
        TemplateEngineInterface $templateEngine,
        ViewInflectorInterface $inflector
    ) {
        $this->beConstructedWith($templateEngine, $inflector);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RendererMiddleware::class);
    }

    function its_a_http_server_middleware()
    {
        $this->shouldBeAnInstanceOf(MiddlewareInterface::class);
    }

    function it_skips_its_turn_if_a_302_status_code_is_already_set(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $response->getStatusCode()
            ->shouldBeCalled()
            ->willReturn(302);

        $this->handle($request, $response)->shouldBe($response);
    }

    function it_uses_inflector_to_determine_the_template_file_name(
        ServerRequestInterface $request,
        ResponseInterface $response,
        TemplateEngineInterface $templateEngine,
        ViewInflectorInterface $inflector,
        Route $route
    ) {
        $request->getAttribute('viewData', [])
            ->shouldBeCalled()
            ->willReturn([]);
        $request->getAttribute('route')
            ->shouldBeCalled()
            ->willReturn($route);
        $inflector->inflect($route)
            ->shouldBeCalled()
            ->willReturn('test/file.twig');
        $templateEngine->parse('test/file.twig')
            ->shouldBeCalled()
            ->willReturn($templateEngine);
        $templateEngine->process([])
            ->shouldBeCalled()
            ->willReturn('Hello world!');
        $response->getStatusCode()->willReturn(200);
        $response->withBody(
            Argument::that(function (Stream $argument) {
                $argument->rewind();
                return $argument->getContents() === 'Hello world!';
            })
        )
            ->shouldBeCalled()
            ->willReturn($response);

        $this->handle($request, $response)->shouldBe($response);
    }
}
