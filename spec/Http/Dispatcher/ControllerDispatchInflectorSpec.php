<?php

namespace spec\Slick\WebStack\Http\Dispatcher;

use Aura\Router\Route;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Wrapper\Subject;
use Slick\WebStack\Http\Dispatcher\ControllerDispatch;
use Slick\WebStack\Http\Dispatcher\ControllerDispatchInflector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Http\Dispatcher\ControllerDispatchInflectorInterface;

class ControllerDispatchInflectorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ControllerDispatchInflector::class);
    }

    function its_a_controller_class_inflector()
    {
        $this->shouldBeAnInstanceOf(ControllerDispatchInflectorInterface::class);
    }

    function it_inflects_controller_dispatch_form_route_attributes()
    {
        $route = new Route();
        $route->attributes([
            'namespace' => 'Controller',
            'controller' => 'pages',
            'action' => 'index',
            'args' => [123, 'test']
        ]);
        $this->inflect($route)->shouldBeAnInstanceOf(ControllerDispatch::class);
    }

    function it_converts_dashed_url_controller_param_into_controller_class_names()
    {
        $route = new Route();
        $route->attributes([
            'namespace' => 'Controller',
            'controller' => 'yellow-pages',
            'action' => 'index'
        ]);
        $this->inflect($route)->shouldHaveControllerNamed('Controller\YellowPages');
    }

    function it_converts_underscored_url_controller_param_into_controller_class_names()
    {
        $route = new Route();
        $route->attributes([
            'namespace' => 'Controller',
            'controller' => 'yellow_pages',
            'action' => 'index'
        ]);
        $this->inflect($route)->shouldHaveControllerNamed('Controller\YellowPages');
    }


    public function getMatchers()
    {
        return [
            'haveControllerNamed' => function (ControllerDispatch $subject, $name) {

                if ($subject->getControllerClassName() !== $name) {
                    throw new FailureException(
                        "Expected controller name to be '{$name}', ".
                        "but got {$subject->getControllerClassName()}"
                    );
                }
                return true;
            }
        ];
    }
}
