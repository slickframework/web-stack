<?php

namespace spec\Slick\WebStack\Console\Service;

use Slick\Di\ContainerInterface;
use Slick\WebStack\Console\Service\ContainerFactory;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Command\Command;

class ContainerFactorySpec extends ObjectBehavior
{
    function let(Command $command)
    {
        $this->beConstructedThrough('create', [$command]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContainerFactory::class);
    }

    function it_creates_a_dependency_container(Command $command)
    {
        $this->beConstructedThrough('create', [$command]);
        $this->container()->shouldBeAnInstanceOf(ContainerInterface::class);
        $this->container()->get(Command::class)->shouldBe($command);
    }
}
