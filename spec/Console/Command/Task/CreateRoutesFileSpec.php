<?php

namespace spec\Slick\WebStack\Console\Command\Task;

use League\Flysystem\FilesystemInterface;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;
use Slick\WebStack\Console\Command\Task\CreateRoutesFile;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Console\Command\TaskInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateRoutesFileSpec extends ObjectBehavior
{
    function let(
        TemplateEngineInterface $templateEngine,
        FilesystemInterface $filesystem
    )
    {
        $templateEngine->parse(Argument::type('string'))
            ->willReturn($templateEngine);
        $templateEngine->process(Argument::type('array'))
            ->willReturn('test');
        $namespace = new NameSpaceEntry("Foo\\Bar\\", 'app/src', getcwd());

        $filesystem->has(Argument::type('string'))
            ->willReturn(false);
        $filesystem->write(Argument::type('string'), 'test')
            ->willReturn(true);

        $this->beConstructedWith(
            $namespace,
            'Infrastructure/WebUI',
            $templateEngine,
            $filesystem
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateRoutesFile::class);
    }

    function its_a_command_task()
    {
        $this->shouldBeAnInstanceOf(TaskInterface::class);
    }

    function it_creates_the_content_rendering_a_template(
        TemplateEngineInterface $templateEngine,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->execute($input, $output);
        $templateEngine->parse(CreateRoutesFile::TEMPLATE_FILE)
            ->shouldHaveBeenCalled();
        $templateEngine->process(
            [
                'namespace' => 'Foo\Bar\Infrastructure\WebUI\Controller'
            ]
        )
            ->shouldHaveBeenCalled();
    }

    function it_will_write_the_contents_to_the_routes_file(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->execute($input, $output);
        $filesystem->write(
            'app/src/Infrastructure/WebUI/Service/routes.yml',
            'test'
        )->shouldHaveBeenCalled();
    }

    function it_deletes_any_existing_routes_file(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $file = 'app/src/Infrastructure/WebUI/Service/routes.yml';
        $filesystem->has($file)
            ->shouldBeCalled()->willReturn(true);
        $filesystem->delete($file)->shouldBeCalled();
        $this->execute($input, $output);
    }
}
