<?php

namespace spec\Slick\WebStack\Console\Command\Task;

use League\Flysystem\FilesystemInterface;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;
use Slick\WebStack\Console\Command\Task\CreatePagesController;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Console\Command\TaskInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreatePagesControllerSpec extends ObjectBehavior
{

    function let(
        TemplateEngineInterface $templateEngine,
        FilesystemInterface $filesystem
    )
    {
        $templateEngine->parse(Argument::type('string'))->willReturn($templateEngine);
        $templateEngine->process(Argument::type('array'))->willReturn('test');
        $namespace = new NameSpaceEntry("Foo\\Bar\\", 'app/src', getcwd());
        $filesystem->has(Argument::type('string'))->willReturn(false);
        $filesystem->write(Argument::type('string'), 'test')->willReturn(true);

        $this->beConstructedWith(
            $namespace,
            'Infrastructure/WebUI',
            $templateEngine,
            $filesystem
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreatePagesController::class);
    }

    function its_a_command_task()
    {
        $this->shouldBeAnInstanceOf(TaskInterface::class);
    }

    function it_uses_the_a_template_engine_to_render_the_controller_content(
        TemplateEngineInterface $templateEngine,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->execute($input, $output);
        $templateEngine->parse(CreatePagesController::TEMPLATE_FILE)
            ->shouldHaveBeenCalled();
        $templateEngine->process([
            'appName' => 'Foo\Bar',
            'namespace' => 'Foo\Bar\Infrastructure\WebUI\Controller'
        ])
            ->shouldHaveBeenCalled();
    }

    function it_writes_the_rendered_content_to_controller_file(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->execute($input, $output);
        $filesystem->write('app/src/Infrastructure/WebUI/Controller/Pages.php', 'test')
            ->shouldHaveBeenCalled();
    }

    function it_deletes_an_existing_controller(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $file = 'app/src/Infrastructure/WebUI/Controller/Pages.php';
        $filesystem->has($file)->shouldBeCalled()->willReturn(true);
        $filesystem->delete($file)->shouldBeCalled();
        $this->execute($input, $output);
    }
}
