<?php

namespace spec\Slick\WebStack;

use Slick\WebStack\Controller;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ControllerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf(TestController::class);
    }

    function it_accepts_a_controller_context(
        Controller\ControllerContextInterface $context
    ) {
        $this->setContext($context)->shouldBe($this->getWrappedObject());
        $this->getContext()->shouldBe($context);
    }

    function it_can_set_view_data_model_variables()
    {
        $this->set('foo', 'bar')->shouldBe($this->getWrappedObject());
        $this->getViewData()->shouldHaveKeyWithValue('foo', 'bar');
    }

    function it_can_set_multiple_view_data_model_variables_passing_an_array()
    {
        $foo = 'bar';
        $bas = 'baz';
        $this->set(compact('foo', 'bas'));
        $this->getViewData()->shouldHaveKeyWithValue('bas', 'baz');
    }
}

class TestController extends Controller
{

    public function getContext()
    {
        return $this->context;
    }
}
