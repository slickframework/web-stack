<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Controller;

use Slick\WebStack\Controller\ControllerMethods;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\ControllerInterface;

/**
 * ControllerMethodsSpec specs
 *
 * @package spec\Slick\WebStack\Controller
 */
class ControllerMethodsSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf(MyController::class);
    }

    function it_holds_the_data_to_be_used_in_views()
    {
        $this->data()->shouldBeArray();
    }

    function it_can_set_a_single_view_variable()
    {
        $this->set('foo', 'bar')->shouldBeAnInstanceOf($this->getWrappedObject());
        $this->data()->shouldHaveKeyWithValue('foo', 'bar');
    }

    function it_can_set_an_array_of_values()
    {
        $this->set(['foo' => 'bar', 'bar' => 'baz']);
        $this->data()->shouldHaveKeyWithValue('foo', 'bar');
        $this->data()->shouldHaveKeyWithValue('bar', 'baz');
    }

    function it_can_be_used_with_compact()
    {
        $foo = 'bar';
        $this->set(compact('foo'));
        $this->data()->shouldHaveKeyWithValue('foo', 'bar');
    }
}

class MyController implements ControllerInterface
{
    use ControllerMethods;
}