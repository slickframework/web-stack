<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Command\Task;

use League\Flysystem\FilesystemInterface;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;
use Slick\WebStack\Console\Command\TaskInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CreatePagesController
 *
 * @package Slick\WebStack\Console\Command\Task
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CreatePagesController implements TaskInterface
{

    const TEMPLATE_FILE = 'templates/console/init/controller.twig';

    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * @var NameSpaceEntry
     */
    private $namespace;
    /**
     * @var string
     */
    private $path;
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * CreatePagesController
     *
     * @param NameSpaceEntry          $namespace
     * @param string                  $path
     * @param TemplateEngineInterface $templateEngine
     * @param FilesystemInterface     $filesystem
     */
    public function __construct(
        NameSpaceEntry $namespace,
        $path,
        TemplateEngineInterface $templateEngine,
        FilesystemInterface $filesystem
    )
    {
        $this->templateEngine = $templateEngine;
        $this->namespace = $namespace;
        $this->path = $path;
        $this->filesystem = $filesystem;
    }

    /**
     * Executes the current task.
     *
     * This method can return the task execution result. For example if this
     * task is asking for user input it should return its input.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $content = $this->content();
        $file = $this->filePath() . '/Pages.php';

        if ($this->filesystem->has($file)) {
            $this->filesystem->delete($file);
        }

        return $this->filesystem->write($file, $content);
    }

    private function content()
    {
        return $this->templateEngine
            ->parse(self::TEMPLATE_FILE)
            ->process([
                'appName' => $this->appName(),
                'namespace' => $this->fullNamespace()
            ]);
    }

    /**
     * Get file full name space
     *
     * @return string
     */
    private function fullNamespace()
    {
        $path = str_replace(
            '//',
            '/',
            $this->path.'/'.CreateRoutesFile::CONTROLLER_PATH
        );
        $path = str_replace('/', "\\", $path);
        return "{$this->namespace->getNameSpace()}{$path}";
    }

    /**
     * Get application name
     *
     * @return string
     */
    private function appName()
    {
        return trim($this->namespace->getNameSpace(), '\\');
    }

    /**
     * Get the path where to put the services file
     *
     * @return string
     */
    private function filePath()
    {
        $path = $this->namespace->getPath();
        $path .= "/{$this->path}/".CreateRoutesFile::CONTROLLER_PATH;
        return str_replace('//', '/', $path);
    }
}