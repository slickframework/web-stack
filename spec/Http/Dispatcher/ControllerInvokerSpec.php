<?php

namespace spec\Slick\WebStack\Http\Dispatcher;

use PhpSpec\ObjectBehavior;
use Slick\WebStack\Controller\ControllerContextInterface;
use Slick\WebStack\ControllerInterface;
use Slick\WebStack\Exception\UndefinedControllerMethodException;
use Slick\WebStack\Http\Dispatcher\ControllerDispatch;
use Slick\WebStack\Http\Dispatcher\ControllerInvoker;
use Slick\WebStack\Http\Dispatcher\ControllerInvokerInterface;

/**
 * ControllerInvoker Spec
 *
 * @package spec\Slick\WebStack\Http\Dispatcher
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ControllerInvokerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ControllerInvoker::class);
    }

    function its_a_controller_invoker_implementation()
    {
        $this->shouldBeAnInstanceOf(ControllerInvokerInterface::class);
    }

    function it_uses_the_controller_dispatch_to_invoke_the_controller_handler(
        InvokedController $controller
    )
    {
        $data = ['foo' => 'bar'];
        $controllerDispatch = new ControllerDispatch(
            InvokedController::class,
            'index',
            [123]
        );
        $controller->getViewData()
            ->shouldBeCalled()
            ->willReturn($data);
        $controller->index(123)->shouldBeCalled();
        $this->invoke($controller, $controllerDispatch)->shouldReturn($data);
    }

    function it_throws_undefined_controller_method_exception_if_method_is_not_defined_in_controller()
    {
        $controller = new InvokedController();
        $controllerDispatch = new ControllerDispatch(
            InvokedController::class,
            'unknown',
            [123]
        );
        $this->shouldThrow(UndefinedControllerMethodException::class)
            ->during('invoke', [$controller, $controllerDispatch]);

    }
}

class InvokedController implements ControllerInterface
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
