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
 * CreateIndexFile
 *
 * @package Slick\WebStack\Console\Command\Task
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CreateIndexFile implements TaskInterface
{

    const TEMPLATE_FILE = 'templates/console/init/index.twig';
    const SERVICES_PATH = 'Service/Definition';

    /**
     * @var NameSpaceEntry
     */
    private $namespace;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;
    /**
     * @var string
     */
    private $webRoot;
    /**
     * @var string
     */
    private $path;

    /**
     * Creates a create Index File task
     *
     * @param NameSpaceEntry          $namespace
     * @param string                  $webRoot
     * @param string                  $path
     * @param FilesystemInterface     $filesystem
     * @param TemplateEngineInterface $templateEngine
     */
    public function __construct(
        NameSpaceEntry $namespace,
        $webRoot,
        $path,
        FilesystemInterface $filesystem,
        TemplateEngineInterface $templateEngine
    )
    {
        $templateEngine->parse(self::TEMPLATE_FILE);
        $this->namespace = $namespace;
        $this->filesystem = $filesystem;
        $this->templateEngine = $templateEngine;
        $this->webRoot = $webRoot;
        $this->path = $path;
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
        $file = "{$this->webRoot}/index.php";

        $this->verifyDirectory();
        $this->deleteExistingFile($file);

        return $this->filesystem->write($file, $content);
    }

    /**
     * Get index file content
     *
     * @return string
     */
    private function getContent()
    {
        return $this->templateEngine
            ->process(
                [
                    'appName' => trim($this->namespace->getNameSpace(), "\\"),
                    'rootPath' => TaskTools::getDirName($this->webRoot),
                    'servicesPath' => $this->getServicesPath()
                ]
            )
        ;
    }

    /**
     * Get services path
     *
     * @return string
     */
    private function getServicesPath()
    {
        $path = trim($this->path, '/');
        return str_replace('//', '/', "/{$this->namespace->getPath()}/{$path}/".
            self::SERVICES_PATH);
    }

    private function verifyDirectory()
    {
        if (!$this->filesystem->has($this->webRoot)) {
            $this->filesystem->createDir($this->webRoot);
        }
    }

    /**
     * Delete the index.php if it exists
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