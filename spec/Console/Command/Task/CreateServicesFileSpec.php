<?php

namespace spec\Slick\WebStack\Console\Command\Task;

use League\Flysystem\FilesystemInterface;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;
use Slick\WebStack\Console\Command\Task\CreateServicesFile;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Console\Command\TaskInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateServicesFileSpec extends ObjectBehavior
{

    function let(
        TemplateEngineInterface $templateEngine,
        FilesystemInterface $filesystem
    )
    {
        $namespace = new NameSpaceEntry("Foo\\Bar\\", 'app/src', getcwd());
        $templateEngine->parse(Argument::type('string'))
            ->willReturn($templateEngine);
        $templateEngine->process(Argument::type('array'))
            ->willReturn('Test content');
        $filesystem->has(Argument::type('string'))
            ->willReturn(true);
        $filesystem->write(Argument::type('string'), 'Test content')
            ->willReturn(true);
        $filesystem->has('app/src/Infrastructure/WebUI/Service/Definition/services.php')
            ->willReturn(false);

        $this->beConstructedWith(
            $namespace,
            'Infrastructure/WebUI',
            $templateEngine,
            $filesystem
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateServicesFile::class);
    }

    function its_a_command_task()
    {
        $this->shouldBeAnInstanceOf(TaskInterface::class);
    }

    function it_creates_a_services_file_content_from_template(
        TemplateEngineInterface $templateEngine,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $templateEngine->parse(CreateServicesFile::TEMPLATE_FILE)
            ->shouldBeCalled()
            ->willReturn($templateEngine);
        $templateEngine->process(
            [
                'appName' => 'Foo\Bar',
                'namespace' => 'Foo\Bar\Infrastructure\WebUI\Service\Definition',
                'templatesPath' => 'dirname(dirname(dirname(dirname(dirname(__DIR__))))).\'/templates\''
            ]
        )->shouldBeCalled()
            ->willReturn('Test content');
        $this->execute($input, $output);
    }

    function it_creates_directory_if_it_does_not_exists(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $filesystem->has('app/src/Infrastructure/WebUI/Service/Definition')
            ->willReturn(false);
        $filesystem->createDir('app/src/Infrastructure/WebUI/Service/Definition')
            ->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_writes_rendered_content_into_services_file(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->execute($input, $output);
        $filesystem->write(
            'app/src/Infrastructure/WebUI/Service/Definition/services.php',
            'Test content'
        )
            ->shouldHaveBeenCalled();
    }

    function it_removes_an_existing_services_file(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $filesystem->has('app/src/Infrastructure/WebUI/Service/Definition/services.php')
            ->shouldBeCalled()
            ->willReturn(true);
        $filesystem->delete('app/src/Infrastructure/WebUI/Service/Definition/services.php')
            ->shouldBeCalled();
        $this->execute($input, $output);
    }
}
