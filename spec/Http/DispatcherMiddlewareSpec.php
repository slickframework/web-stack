<?php

namespace spec\Slick\WebStack\Http;

use Aura\Router\Route;
use PhpSpec\ObjectBehavior;
use PhpSpec\Wrapper\Collaborator;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Di\ContainerInterface;
use Slick\Di\Exception\ClassNotFoundException;
use Slick\Http\Response;
use Slick\Http\Server\MiddlewareInterface;
use Slick\WebStack\Controller\Context;
use Slick\WebStack\Controller\ControllerContextInterface;
use Slick\WebStack\ControllerInterface;
use Slick\WebStack\Exception\ControllerNotFoundException;
use Slick\WebStack\Http\Dispatcher\ControllerDispatch;
use Slick\WebStack\Http\Dispatcher\ControllerDispatchInflectorInterface;
use Slick\WebStack\Http\Dispatcher\ControllerInvokerInterface;
use Slick\WebStack\Http\DispatcherMiddleware;
use Slick\WebStack\Service\FlashMessages;

class DispatcherMiddlewareSpec extends ObjectBehavior
{

    /**
     * @var ControllerDispatchInflectorInterface|Collaborator
     */
    private $dispatchInflector;

    /**
     * @var ControllerInvokerInterface|Collaborator
     */
    private $invokerMock;

    /**
     * @var ContainerInterface|Collaborator
     */
    private $containerMock;

    /**
     * @var TestController|Collaborator
     */
    private $controllerMock;

    /**
     * @var Collaborator|ControllerContextInterface
     */
    private $contextMock;

    /**
     * Set SUT object
     *
     * @param Collaborator|ControllerDispatchInflectorInterface $controllerDispatchInflector
     * @param Collaborator|ControllerInvokerInterface $invoker
     * @param Collaborator|ContainerInterface $container
     * @param Collaborator|TestController $controller
     * @param Collaborator|ControllerContextInterface $context
     */
    function let(
        ControllerDispatchInflectorInterface $controllerDispatchInflector,
        ControllerInvokerInterface $invoker,
        ContainerInterface $container,
        TestController $controller,
        ControllerContextInterface $context
    ) {
        $this->beConstructedWith(
            $controllerDispatchInflector,
            $invoker,
            $container
        );
        $this->dispatchInflector = $controllerDispatchInflector;
        $this->invokerMock = $invoker;
        $this->controllerMock = $controller;
        $this->containerMock = $container;
        $this->contextMock = $context;

        $dispatch = new ControllerDispatch('controllerClass', 'index', [123]);
        $controllerDispatchInflector
            ->inflect(Argument::any())
            ->willReturn($dispatch);

        $container->get(FlashMessages::class)
            ->willReturn(true);
        $container->make("controllerClass")->willReturn($controller);
        $container->has('controller.context.class')->willReturn(false);
        $container->make(DispatcherMiddleware::CONTEXT_CLASS)
            ->willReturn($context);

        $invoker
            ->invoke($controller, Argument::type(ControllerDispatch::class))
            ->willReturn(["flashMessages" => true]);
        $context->register(
            Argument::type(ServerRequestInterface::class),
            Argument::type(ResponseInterface::class)
        )->willReturn(true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(DispatcherMiddleware::class);
    }

    function its_an_http_middleware()
    {
        $this->shouldBeAnInstanceOf(MiddlewareInterface::class);
    }

    function it_inflects_controller_information_from_http_request(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Route $route
    )
    {
        $this->prepareRequest($request, $route);

        $this->handle($request, $response);
        $this->dispatchInflector
            ->inflect($route)
            ->shouldHaveBeenCalled();
    }

    function it_uses_the_container_to_create_the_controller(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->prepareRequest($request);

        $this->handle($request, $response);
        $this->containerMock
            ->make("controllerClass")
            ->shouldHaveBeenCalled();
    }

    function it_sets_a_context_for_created_controllers(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->prepareRequest($request);

        $this->handle($request, $response);
        $this->controllerMock
            ->setContext($this->contextMock)
            ->shouldHaveBeenCalled();
    }

    function it_passes_the_request_and_response_to_controller_context(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->prepareRequest($request);

        $this->handle($request, $response);
        $this->contextMock
            ->register($request, $response)
            ->shouldHaveBeenCalled();
    }

    function it_uses_invoker_to_invoke_controller_handler(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {

        $this->prepareRequest($request);

        $this->handle($request, $response);
        $this->invokerMock
            ->invoke(
                $this->controllerMock,
                Argument::type(ControllerDispatch::class)
            )
            ->shouldHaveBeenCalled();
    }

    function it_will_create_a_request_with_view_data(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->prepareRequest($request);

        $this->handle($request, $response);
        $request->withAttribute('viewData', ["flashMessages" => true])
            ->shouldHaveBeenCalled();
    }

    function it_will_forward_the_request_from_controller_context(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->prepareRequest($request);

        $this->contextMock->getRequest()
            ->shouldBeCalled()
            ->willReturn($request);
        $this->handle($request, $response);
    }

    function it_will_forward_the_response_from_controller_context(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $this->prepareRequest($request);

        $this->contextMock->getResponse()
            ->shouldBeCalled()
            ->willReturn($response);

        $this->handle($request, $response);
    }

    function it_throws_unknown_controller_exception_for_an_undefined_controller_class(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->prepareRequest($request);
        $this->containerMock
            ->make("controllerClass")
            ->willThrow(new ClassNotFoundException());
        $this->shouldThrow(ControllerNotFoundException::class)
            ->during('handle', [$request, $response]);
    }


    private function prepareRequest(
        ServerRequestInterface $request,
        Route $route = null
    )
    {
        $route = !$route ? new Route() : $route;
        $request->getAttribute('route', false)
            ->willReturn($route);
        $request->withAttribute('viewData', ["flashMessages" => true])
            ->willReturn($request);
        $this->contextMock->getRequest()->willReturn($request);
        $this->contextMock->getResponse()->willReturn(new Response());
    }

}

class TestController implements ControllerInterface
{

    public function setContext(ControllerContextInterface $context)
    {
        // do nothing
        return $this;
    }

    public function set($name, $value = null)
    {
        // do nothing
        return $this;
    }

    public function index($test)
    {
        // Do controller stuff
    }

    /**
     * A view data model used by renderer
     *
     * @return array
     */
    public function getViewData()
    {
        // do nothing
        return [];
    }
}
