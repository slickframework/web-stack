<?php

namespace spec\Slick\WebStack\Console\Command\Task;

use Slick\WebStack\Console\Command\Task\AskForNamespace;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Console\Command\TaskInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class AskForNamespaceSpec extends ObjectBehavior
{

    function let(Command $command, AskForNamespace\ComposerReader $reader)
    {
        $this->beConstructedWith($command, $reader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AskForNamespace::class);
    }

    function its_a_command_task()
    {
        $this->shouldBeAnInstanceOf(TaskInterface::class);
    }

    function it_asks_to_select_from_a_list_of_namespaces(
        Command $command,
        QuestionHelper $helper,
        InputInterface $input,
        OutputInterface $output,
        AskForNamespace\ComposerReader $reader
    )
    {
        $namespace = new AskForNamespace\NameSpaceEntry('Features\\App', '/src');
        $command->getHelper('question')
            ->shouldBeCalled()
            ->willReturn($helper);

        $helper->ask($input, $output, Argument::type(ChoiceQuestion::class))
            ->shouldBeCalled()
            ->willReturn('Features\\App');

        $reader->nameSpaces()->shouldBeCalled()
            ->willReturn(new AskForNamespace\NameSpaceCollection([$namespace]));

        $this->execute($input, $output)
            ->shouldBeAnInstanceOf($namespace);
    }
}
