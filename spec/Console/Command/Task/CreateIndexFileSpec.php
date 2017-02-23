<?php

namespace spec\Slick\WebStack\Console\Command\Task;

use League\Flysystem\FilesystemInterface;
use Prophecy\Argument;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;
use Slick\WebStack\Console\Command\Task\CreateIndexFile;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Console\Command\TaskInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateIndexFileSpec extends ObjectBehavior
{

    function let(
        FilesystemInterface $filesystem,
        TemplateEngineInterface $templateEngine
    )
    {
        $namespace = new NameSpaceEntry('Foo\Bar', 'src', getcwd());
        $webRoot = 'features/app/public';

        $content = 'test';
        $templateEngine->parse(CreateIndexFile::TEMPLATE_FILE)
            ->willReturn($templateEngine);
        $templateEngine->process(Argument::type('array'))
            ->willReturn($content);

        $filesystem->has('features/app/public')->willReturn(true);
        $filesystem->write(Argument::type('string'), $content)
            ->willReturn(true);
        $filesystem->has('features/app/public/index.php')
            ->willReturn(false);

        $this->beConstructedWith($namespace, $webRoot, $filesystem, $templateEngine);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateIndexFile::class);
    }

    function its_a_command_task()
    {
        $this->shouldImplement(TaskInterface::class);
    }

    function it_creates_the_index_file_content_from_template_rendering(
        TemplateEngineInterface $templateEngine,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $templateEngine->process(
            [
                'appName' => 'Foo\Bar',
                'rootPath' => 'dirname(dirname(dirname(__DIR__)))',
                'servicesPath' => '/src/'.CreateIndexFile::SERVICES_PATH
            ]
        )
            ->shouldBeCalled()
            ->willReturn('test');

        $templateEngine->parse(CreateIndexFile::TEMPLATE_FILE)
            ->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_creates_the_web_root_directory_if_it_does_not_exists(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $filesystem->has('features/app/public')->willReturn(false);
        $filesystem->createDir('features/app/public')->shouldBeCalled();
        $this->execute($input, $output);
    }

    function it_write_the_render_content_to_index_php_file(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $this->execute($input, $output);

        $filesystem->write('features/app/public/index.php', 'test')
            ->shouldHaveBeenCalled();
    }

    function it_removes_an_existing_index_file(
        FilesystemInterface $filesystem,
        InputInterface $input,
        OutputInterface $output
    )
    {
        $filesystem->has('features/app/public/index.php')
            ->shouldBeCalled()
            ->willReturn(true);
        $filesystem->delete('features/app/public/index.php')
            ->shouldBeCalled();
        $this->execute($input, $output);
    }
}
