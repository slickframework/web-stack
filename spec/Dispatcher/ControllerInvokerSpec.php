<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Dispatcher;

use Psr\Http\Message\ResponseInterface;
use Slick\Di\ContainerInterface;
use Slick\Http\Message\Response;
use Slick\WebStack\Controller\ControllerContextInterface;
use Slick\WebStack\Controller\ControllerMethods;
use Slick\WebStack\ControllerInterface;
use Slick\WebStack\Dispatcher\ControllerDispatch;
use Slick\WebStack\Dispatcher\ControllerInvoker;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Dispatcher\ControllerInvokerInterface;

/**
 * ControllerInvokerSpec specs
 *
 * @package spec\Slick\WebStack\Dispatcher
 */
class ControllerInvokerSpec extends ObjectBehavior
{
    function let(ContainerInterface $container, InvokedController $controller)
    {
        $container->make(InvokedController::class)
            ->willReturn($controller);
        $this->beConstructedWith($container);
    }

    function it_is_initializable_with_a_dependency_container()
    {
        $this->shouldHaveType(ControllerInvoker::class);
    }

    function its_a_controller_invoker_implementation()
    {
        $this->shouldBeAnInstanceOf(ControllerInvokerInterface::class);
    }

    function it_uses_the_controller_dispatch_to_invoke_the_controller_handler(
        ContainerInterface $container,
        ControllerContextInterface $context
    ) {
        $controller = new InvokedController();
        $container->make(InvokedController::class)->willReturn($controller);
        $data = [null, ['foo' => 'bar']];
        $controllerDispatch = new ControllerDispatch(
            InvokedController::class,
            'index',
            ['bar']
        );

        $this->invoke($context, $controllerDispatch)->shouldBe($data);
    }

    function it_can_returns_the_method_return_as_response(
        ContainerInterface $container,
        ControllerContextInterface $context
    ) {
        $controller = new InvokedController();
        $container->make(InvokedController::class)->willReturn($controller);
        $controllerDispatch = new ControllerDispatch(
            InvokedController::class,
            'responsive'
        );
        $response = $this->invoke($context, $controllerDispatch);
        $response->shouldHaveCount(2);
        $response[1]->shouldBe([]);
        $response[0]->shouldBeAnInstanceOf(ResponseInterface::class);
    }
}

class InvokedController implements ControllerInterface
{
    use ControllerMethods;

    public function index($test)
    {
        $this->set('foo', $test);
    }

    public function responsive(): ResponseInterface
    {
        return new Response(200);
    }
}