<?php

namespace spec\Slick\WebStack\Console\Command\Task;

use League\Flysystem\FilesystemInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;
use Slick\WebStack\Console\Command\Task\CreateIndexFile;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Console\Command\TaskInterface;

class CreateIndexFileSpec extends ObjectBehavior
{

    function let(NameSpaceEntry $namespace, FilesystemInterface $filesystem)
    {
        $this->beConstructedWith($namespace, $filesystem);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateIndexFile::class);
    }

    function it_is_a_command_task()
    {
        $this->shouldImplement(TaskInterface::class);
    }

}
