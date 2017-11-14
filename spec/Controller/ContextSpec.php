<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Controller;

use Aura\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Controller\Context;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Controller\ControllerContextInterface;

/**
 * ContextSpec specs
 *
 * @package spec\Slick\WebStack\Controller
 */
class ContextSpec extends ObjectBehavior
{

    function let(ServerRequestInterface $request)
    {
        $route = (new Route())->attributes(['foo' =>'bar', 'bar' => 'baz']);
        $this->beConstructedWith($request, $route);
    }

    function its_a_controller_context()
    {
        $this->shouldBeAnInstanceOf(ControllerContextInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Context::class);
    }

    function it_can_have_a_list_of_query_params(
        ServerRequestInterface $request
    )
    {
        $data = ['foo' =>'bar', 'bar' => 'baz'];
        $request->getQueryParams()->willReturn($data);
        $this->queryParam()->shouldBe($data);
    }

    function it_can_retrieve_a_single_query_param_value(
        ServerRequestInterface $request
    )
    {
        $data = ['foo' =>'bar', 'bar' => 'baz'];
        $request->getQueryParams()->willReturn($data);
        $this->queryParam('foo')->shouldBe('bar');
    }

    function it_can_have_a_list_of_post_parameters(
        ServerRequestInterface $request
    )
    {
        $data = ['foo' =>'bar', 'bar' => 'baz'];
        $request->getParsedBody()->willReturn($data);
        $this->postParam()->shouldBe($data);
    }

    function it_will_return_a_default_value_for_missing_parameters(
        ServerRequestInterface $request
    )
    {
        $data = ['foo' =>'bar', 'bar' => 'baz'];
        $request->getParsedBody()->willReturn($data);
        $this->postParam('baz', 'foo')->shouldBe('foo');
    }

    function it_can_retrieve_a_route_parameter()
    {
        $this->routeParam('foo')->shouldBe('bar');
    }

    function it_has_a_server_request(ServerRequestInterface $request)
    {
        $this->request()->shouldBe($request);
    }

    function it_can_check_if_current_request_has_a_given_method(
        ServerRequestInterface $request
    )
    {
        $request->getMethod()->willReturn('PUT');
        $this->requestIs('put')->shouldBe(true);
    }

    function it_can_be_set_with_a_response(ResponseInterface $response)
    {
        $this->setResponse($response)->shouldBe($this->getWrappedObject());
        $this->response()->shouldBe($response);
    }

    function it_can_set_a_redirect_response()
    {
        $this->redirect('/some/page');
        $response = $this->response();
        $response->shouldBeAnInstanceOf(ResponseInterface::class);
        $response->getStatusCode()->shouldBe(302);
        $response->getHeaderLine('Location')->shouldBe('/some/page');
        $this->handlesResponse()->shouldBe(true);
    }
}