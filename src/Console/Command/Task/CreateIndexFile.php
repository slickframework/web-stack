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
    const SERVICES_PATH = 'Infrastructure/WebUI/Service/Definition';
    const MODULE        = 'Infrastructure/WebUI';

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
     * Creates a create Index File task
     *
     * @param NameSpaceEntry          $namespace
     * @param string                  $webRoot
     * @param FilesystemInterface     $filesystem
     * @param TemplateEngineInterface $templateEngine
     */
    public function __construct(
        NameSpaceEntry $namespace,
        $webRoot,
        FilesystemInterface $filesystem,
        TemplateEngineInterface $templateEngine
    )
    {
        $templateEngine->parse(self::TEMPLATE_FILE);
        $this->namespace = $namespace;
        $this->filesystem = $filesystem;
        $this->templateEngine = $templateEngine;
        $this->webRoot = $webRoot;
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
                    'appName' => $this->namespace->getNameSpace(),
                    'rootPath' => $this->getDirName($this->webRoot),
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
        return "/{$this->namespace->getPath()}/".self::SERVICES_PATH;
    }

    private function getDirName($path, $from = '__DIR__')
    {
        $parts = explode('/', trim($path, '/'));
        array_pop($parts);

        $expression = "dirname({$from})";

        if (count($parts) > 0) {
            return $this->getDirName(implode('/', $parts), $expression);
        }

        return $expression;
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