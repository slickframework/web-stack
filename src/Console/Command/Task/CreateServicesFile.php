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
 * CreateServicesFile
 *
 * @package Slick\WebStack\Console\Command\Task
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CreateServicesFile implements TaskInterface
{

    const TEMPLATE_FILE = 'templates/console/init/services.twig';
    const SERVICES_PATH = 'Service/Definition';

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
     * Creates a create services file command task
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
        $content = $this->getContent();
        $path = $this->filePath();
        $file = "{$path}/services.php";

        $this->createDirectory($path);

        $this->deleteExistingFile($file);

        $this->filesystem->write($file, $content);

        return true;
    }

    /**
     * Gets services file content
     *
     * @return string
     */
    private function getContent()
    {
        $path = str_replace(
            [$this->namespace->getNameSpace(), "\\"],
            ['src/', '/'],
            $this->fullNamespace()
        );

        $templates  = TaskTools::getDirName($path);
        $templates .= '.\'/templates\'';
        return $this->templateEngine
            ->parse(self::TEMPLATE_FILE)
            ->process([
                'appName' => trim($this->namespace->getNameSpace(), '\\'),
                'namespace' => $this->fullNamespace(),
                'templatesPath' => $templates
            ]);
    }

    /**
     * Get file full name space
     *
     * @return string
     */
    private function fullNamespace()
    {
        $path = str_replace('//', '/', $this->path.'/'.self::SERVICES_PATH);
        $path = str_replace('/', "\\", $path);
        return "{$this->namespace->getNameSpace()}{$path}";
    }

    /**
     * Get the path where to put the services file
     *
     * @return string
     */
    private function filePath()
    {
        $path = $this->namespace->getPath();
        $path .= "/{$this->path}/".self::SERVICES_PATH;
        return str_replace('//', '/', $path);
    }

    /**
     * Creates the services directory if it does not exists
     *
     * @param string $path
     */
    private function createDirectory($path)
    {
        if (!$this->filesystem->has($path)) {
            $this->filesystem->createDir($path);
        }
    }

    /**
     * Deletes services it it exists
     *
     * @param string $file
     */
    private function deleteExistingFile($file)
    {
        if ($this->filesystem->has($file)) {
            $this->filesystem->delete($file);
        }
    }
}