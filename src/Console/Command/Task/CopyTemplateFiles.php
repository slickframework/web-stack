<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Command\Task;

use League\Flysystem\FilesystemInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;
use Slick\WebStack\Console\Command\TaskInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CopyTemplateFiles
 *
 * @package Slick\WebStack\Console\Command\Task
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class CopyTemplateFiles implements TaskInterface
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var string
     */
    private $currentPath;

    /**
     * @var string
     */
    private $path;
    /**
     * @var NameSpaceEntry
     */
    private $namespace;

    /**
     * CopyTemplateFiles
     *
     * @param NameSpaceEntry $namespace
     * @param string $path
     * @param FilesystemInterface $filesystem
     */
    public function __construct(
        NameSpaceEntry $namespace,
        $path,
        FilesystemInterface $filesystem
    )
    {
        $this->filesystem = $filesystem;
        $this->currentPath = dirname(dirname(dirname(dirname(__DIR__)))).
            '/templates/console/files';
        $this->currentPath = str_replace(getcwd(), '', $this->currentPath);
        $this->path = $path;
        $this->namespace = $namespace;
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
        $files = $this->filesystem->listContents($this->currentPath, true);
        foreach ($files as $file) {
            if ($file['type'] == 'dir') {
                continue;
            }
            $this->filesystem->copy($file['path'], $this->getDestination($file));
        }
        return true;
    }

    private function getDestination($file)
    {
        $origin = dirname(
            $this->namespace->getBasePath() .'/'.
            $this->namespace->getPath());
        $origin = str_replace(getcwd().'/', '', $origin);

        $path = str_replace($this->currentPath, '', '/'.$file['path']);
        return $this->checkFile("{$origin}/templates{$path}");
    }

    private function checkFile($file)
    {
        if ($this->filesystem->has($file)) {
            $this->filesystem->delete($file);
        }
        return $file;
    }
}