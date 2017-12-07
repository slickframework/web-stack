<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Dispatcher;

use Aura\Router\Route;
use PhpSpec\Exception\Example\FailureException;
use Slick\WebStack\Controller\ControllerMethods;
use Slick\WebStack\ControllerInterface;
use Slick\WebStack\Dispatcher\ControllerDispatch;
use Slick\WebStack\Dispatcher\ControllerDispatchInflector;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Dispatcher\ControllerDispatchInflectorInterface;

/**
 * ControllerDispatchInflectorSpec specs
 *
 * @package spec\Slick\WebStack\Dispatcher
 */
class ControllerDispatchInflectorSpec extends ObjectBehavior
{

    function its_a_controller_inflector()
    {
        $this->shouldBeAnInstanceOf(ControllerDispatchInflectorInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ControllerDispatchInflector::class);
    }

    function it_inflects_controller_dispatch_from_route_attributes()
    {
        $route = new Route();
        $route->attributes([
            'namespace' => 'spec\Slick\WebStack\Dispatcher',
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
            'namespace' => 'spec\Slick\WebStack\Dispatcher',
            'controller' => 'yellow-pages',
            'action' => 'index'
        ]);
        $this->inflect($route)->shouldHaveControllerNamed('spec\Slick\WebStack\Dispatcher\YellowPages');
    }
    function it_converts_underscored_url_controller_param_into_controller_class_names()
    {
        $route = new Route();
        $route->attributes([
            'namespace' => 'spec\Slick\WebStack\Dispatcher',
            'controller' => 'yellow_pages',
            'action' => 'index'
        ]);
        $this->inflect($route)->shouldHaveControllerNamed('spec\Slick\WebStack\Dispatcher\YellowPages');
    }
    public function getMatchers()
    {
        return [
            'haveControllerNamed' => function (ControllerDispatch $subject, $name) {
                if ($subject->controllerClass()->getName() !== $name) {
                    throw new FailureException(
                        "Expected controller name to be '{$name}', ".
                        "but got {$subject->controllerClass()->getName()}"
                    );
                }
                return true;
            }
        ];
    }
}

class Pages implements ControllerInterface
{
    use ControllerMethods;

    public function index()
    {

    }
}

class YellowPages extends Pages
{
}