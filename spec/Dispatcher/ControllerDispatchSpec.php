<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Dispatcher;

use Slick\WebStack\Controller\ControllerMethods;
use Slick\WebStack\ControllerInterface;
use Slick\WebStack\Dispatcher\ControllerDispatch;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Exception\BadControllerClassException;
use Slick\WebStack\Exception\ControllerNotFoundException;
use Slick\WebStack\Exception\MissingControllerMethodException;

/**
 * ControllerDispatchSpec specs
 *
 * @package spec\Slick\WebStack\Dispatcher
 */
class ControllerDispatchSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(DummyController::class, 'handle', ['1', '2']);
    }

    function it_is_initializable_with_a_controller_class_name_method_and_optional_arguments()
    {
        $this->shouldHaveType(ControllerDispatch::class);
    }

    function it_has_a_controller_class_reflection()
    {
        $this->controllerClass()->shouldBeAnInstanceOf(\ReflectionClass::class);
    }

    function it_throws_an_exception_when_provided_controller_class_does_not_exists()
    {
        $this->beConstructedWith('testClass', 'someMethod');
        $this->shouldThrow(ControllerNotFoundException::class)->duringInstantiation();
    }

    function it_throws_an_exception_when_controller_class_is_not_a_controller()
    {
        $this->beConstructedWith(\stdClass::class, 'someMethod');
        $this->shouldThrow(BadControllerClassException::class)->duringInstantiation();
    }

    function it_has_a_request_handler_method_reflection_()
    {
        $this->handlerMethod()->shouldBeAnInstanceOf(\ReflectionMethod::class);
    }

    function it_throws_an_exception_for_missing_methods()
    {
        $this->beConstructedWith(DummyController::class, 'someMethod');
        $this->shouldThrow(MissingControllerMethodException::class)->duringInstantiation();
    }

    function it_has_a_list_of_optional_arguments()
    {
        $this->arguments()->shouldBe(['1', '2']);
    }
}

class DummyController implements ControllerInterface
{
    use ControllerMethods;

    public function handle()
    {

    }
}