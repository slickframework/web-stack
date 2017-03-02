<?php

namespace spec\Slick\WebStack\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Di\ContainerInjectionInterface;
use Slick\Http\Uri;
use Slick\WebStack\Controller\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Controller\ControllerContextInterface;
use Slick\WebStack\Service\UriGeneratorInterface;

class ContextSpec extends ObjectBehavior
{

    function let(UriGeneratorInterface $uriGenerator)
    {
        $this->beConstructedWith($uriGenerator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Context::class);
    }

    function its_a_controller_context()
    {
        $this->shouldBeAnInstanceOf(ControllerContextInterface::class);
    }

    function it_implements_a_container_injection_for_initialization()
    {
        $this->shouldImplement(ContainerInjectionInterface::class);
    }

    function it_uses_an_http_request_and_response(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->register($request, $response)->shouldBe($this->getWrappedObject());
    }

    function it_retrieves_the_server_parameters_on_post(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $request->getServerParams()->willReturn(
            [
                'foo' => 'bar',
                'bar' => 'baz'
            ]
        );
        $this->register($request, $response);
        $this->getPost()->shouldBe([
            'foo' => 'bar',
            'bar' => 'baz'
        ]);
    }

    function it_can_retrieve_a_single_named_parameter(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $request->getServerParams()->willReturn(
            [
                'foo' => 'bar',
                'bar' => 'baz'
            ]
        );
        $this->register($request, $response);
        $this->getPost('bar')->shouldBe('baz');
    }

    function it_returns_a_default_value_if_param_is_not_set(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $request->getServerParams()->willReturn(
            [
                'foo' => 'bar',
                'bar' => 'baz'
            ]
        );
        $this->register($request, $response);
        $this->getPost('boo', 'test')->shouldBe('test');
    }

    function it_can_get_URL_parameters(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $request->getQueryParams()->willReturn(
            [
                'foo' => 'bar',
                'bar' => 'baz'
            ]
        );
        $this->register($request, $response);
        $this->getQuery('boo', 'test')->shouldBe('test');
        $this->getQuery('bar')->shouldBe('baz');
        $this->getQuery()->shouldBe([
            'foo' => 'bar',
            'bar' => 'baz'
        ]);
    }

    function it_can_set_a_redirecting_http_response(
        ServerRequestInterface $request,
        ResponseInterface $response,
        UriGeneratorInterface $uriGenerator
    )
    {
        $this->beConstructedWith($uriGenerator);

        $location = 'home';
        $response->withStatus(302)->willReturn($response);
        $response->withHeader('location', '/pages/index')
            ->shouldBeCalled()
            ->willReturn($response);
        $uriGenerator->setRequest($request)
            ->shouldBeCalled();
        $uriGenerator->generate($location, [])
            ->shouldBeCalled()
            ->willReturn(new Uri('/pages/index'));

        $this->register($request, $response);
        $this->redirect($location);

        $response->withStatus(302)->shouldHaveBeenCalled();
    }

    function it_can_set_the_disable_rendering_flag_on_request(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $request->withAttribute('rendering', false)->willReturn($request);
        $this->register($request, $response);
        $this->disableRendering()->shouldBe($this->getWrappedObject());
        $request->withAttribute('rendering', false)->shouldHaveBeenCalled();
    }

    function it_can_set_the_rendering_view(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->register($request, $response);

        $request->withAttribute('view', 'path/to/view.twig')
            ->shouldBeCalled()
            ->willReturn($request);
        $this->setView('path/to/view.twig')->shouldReturn($this->getWrappedObject());
    }

}
