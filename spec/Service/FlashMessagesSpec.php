<?php

namespace spec\Slick\WebStack\Service;

use PhpSpec\ObjectBehavior;
use Slick\Http\SessionDriverInterface;
use Slick\WebStack\Service\FlashMessages;

class FlashMessagesSpec extends ObjectBehavior
{
    function let(SessionDriverInterface $sessionDriver)
    {
        $this->beConstructedWith($sessionDriver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FlashMessages::class);
    }

    function it_registers_a_message_with_provided_type(
        SessionDriverInterface $sessionDriver
    )
    {
        $this->set(FlashMessages::TYPE_INFO, 'Test!')
            ->shouldBe($this->getWrappedObject());
        $sessionDriver->set('_messages_', [FlashMessages::TYPE_INFO => ['Test!']])
            ->shouldHaveBeenCalled();
    }

    function it_registers_an_info_message_when_an_unknown_type_is_given(
        SessionDriverInterface $sessionDriver
    )
    {
        $sessionDriver->set('_messages_', [FlashMessages::TYPE_INFO => ['Test!', 'Test!']])
            ->shouldBeCalled();
        $this->set('other', 'Test!')
            ->shouldBe($this->getWrappedObject());
    }

    function it_flushes_all_messages_when_they_are_retrieved(
        SessionDriverInterface $sessionDriver
    )
    {
        $messages = [1=>['test']];
        $sessionDriver->get('_messages_', [])->shouldBeCalled()
            ->willReturn($messages);

        $sessionDriver->erase('_messages_')->shouldBeCalled();
        $this->messages()->shouldBe($messages);
    }
}
