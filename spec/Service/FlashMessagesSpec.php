<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Service;

use PhpSpec\Exception\Example\FailureException;
use Prophecy\Argument;
use Slick\Http\Session\SessionDriverInterface;
use Slick\WebStack\Service\FlashMessages;
use PhpSpec\ObjectBehavior;

/**
 * FlashMessagesSpec specs
 *
 * @package spec\Slick\WebStack\Service
 */
class FlashMessagesSpec extends ObjectBehavior
{
    function let(SessionDriverInterface $sessionDriver)
    {
        $sessionDriver->get('_messages_')->shouldBeCalled()->willReturn([]);
        $sessionDriver->set(Argument::type('string'), Argument::any())->willReturn($sessionDriver);
        $sessionDriver->erase(Argument::type('string'))->willReturn($sessionDriver);

        $this->beConstructedWith($sessionDriver);

    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FlashMessages::class);
    }

    function it_can_set_messages_of_a_given_type(SessionDriverInterface $sessionDriver)
    {

        $this->set(FlashMessages::TYPE_INFO, 'Info messages')
            ->shouldBe($this->getWrappedObject());
        $sessionDriver->set('_messages_', [FlashMessages::TYPE_INFO => ['Info messages']])
            ->shouldHaveBeenCalled();
    }

    function it_cast_to_info_a_message_set_with_an_unknown_type(SessionDriverInterface $sessionDriver)
    {
        $this->flush();
        $this->set('Some Type', 'Info messages')
            ->shouldBe($this->getWrappedObject());
        $sessionDriver->set('_messages_', [FlashMessages::TYPE_INFO => ['Info messages']])
            ->shouldHaveBeenCalled();
    }

    function it_can_retrieve_all_messages()
    {
        $this->set(FlashMessages::TYPE_INFO, 'Info messages');
        $this->messages()
            ->shouldHaveKey(FlashMessages::TYPE_INFO);
    }

    function it_flushes_all_messages_when_retrieving_them()
    {
        $this->messages()->shouldBeEmpty();
    }

    function it_can_flush_all_messages(SessionDriverInterface $sessionDriver)
    {
        $this->set(FlashMessages::TYPE_ERROR, 'Error message');
        $this->flush();
        $this->messages()->shouldBeEmpty();
        $sessionDriver->erase('_messages_')->shouldHaveBeenCalled();
    }

    public function getMatchers()
    {
        return [
            'beEmpty' => function(array $subject) {
                if (! empty($subject)) {
                    $count = count($subject);
                    throw new FailureException("Expecting and empty array, but got one with $count elements");
                }
                return true;
            }
        ];
    }
}