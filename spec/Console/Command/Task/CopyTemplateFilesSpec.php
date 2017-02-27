<?php

namespace spec\Slick\WebStack\Console\Command\Task;

use League\Flysystem\FilesystemInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;
use Slick\WebStack\Console\Command\Task\CopyTemplateFiles;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Console\Command\TaskInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CopyTemplateFilesSpec extends ObjectBehavior
{
    function let(
        FilesystemInterface $filesystem
    )
    {
        $namespace = new NameSpaceEntry("Foo\\Bar\\", 'app/src', getcwd());
        $filesystem->listContents(Argument::type('string'), true)
            ->willReturn($this->getFiles());
        $filesystem->has(Argument::type('string'))->willReturn(false);
        $filesystem->copy(Argument::type('string'), Argument::type('string'))
            ->willReturn(true);
        $this->beConstructedWith(
            $namespace,
            'Infrastructure/WebUI',
            $filesystem
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CopyTemplateFiles::class);
    }

    function its_a_command_task()
    {
        $this->shouldBeAnInstanceOf(TaskInterface::class);
    }

    function it_copies_the_template_dir_to_the_templates_path(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->execute($input, $output)->shouldBe(true);
        $filesystem->copy(
            Argument::type('string'),
            Argument::type('string')
        )->shouldHaveBeenCalledTimes(2);
    }

    private function getFiles()
    {
        return [
            [
                'type' => 'file',
                'path' => 'templates/console/files/default-layout.twig',
                'dirname' => 'templates/console/files',
                'basename' => 'default-layout.twig'
            ],
            [
                'type' => 'dir',
                'path' => 'templates/console/files/pages',
                'dirname' => 'templates/console/files',
                'basename' => 'pages'
            ],
            [
                'type' => 'file',
                'path' => 'templates/console/files/pages/home.twig',
                'dirname' => 'templates/console/files/pages',
                'basename' => 'home.twig'
            ],
        ];
    }
}
