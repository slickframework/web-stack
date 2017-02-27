<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Command;

use Slick\Di\ContainerInterface;
use Slick\WebStack\Console\Command\Task\AskForNamespace;
use Slick\WebStack\Console\Command\Task\AskForWebRoot;
use Slick\WebStack\Console\Command\Task\CopyTemplateFiles;
use Slick\WebStack\Console\Command\Task\CreateIndexFile;
use Slick\WebStack\Console\Command\Task\CreatePagesController;
use Slick\WebStack\Console\Command\Task\CreateRoutesFile;
use Slick\WebStack\Console\Command\Task\CreateServicesFile;
use Slick\WebStack\Console\Service\ContainerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * BuildWebApp
 *
 * @package Slick\WebStack\Console\Command
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class BuildWebApp extends Command
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Builds a basic, startup files and directory structure for a web application.')
            ->addArgument('path', InputArgument::REQUIRED, 'Where will your web application files live in?')
        ;
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $this->input = $input;
        $this->output = $output;

        $this->printBanner($this->output);

        $webRoot = $this->container()
            ->get(AskForWebRoot::class)
            ->execute($this->input, $this->output);
        $nameSpace = $this->container()
            ->get(AskForNamespace::class)
            ->execute($this->input, $this->output);

        $this->createIndexFile($nameSpace, $webRoot);
        $this->createServicesFile($nameSpace);
        $this->createRoutesFile($nameSpace);
        $this->createController($nameSpace);

        $copyFiles = $this->container()->make(
            CopyTemplateFiles::class,
            $nameSpace,
            $this->input->getArgument('path'),
            '@local.filesystem'
        );
        $copyFiles->execute($this->input, $this->output);

        $this->printSuccess($this->output);
        return 0;
    }

    private function printBanner(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<info>Slick web application initialization</info>');
        $output->writeln('<info>------------------------------------</info>');
    }

    private function printSuccess(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<info>Web application structured and bootstrapped!</info>');
        $output->writeln('');
    }

    /**
     * Get container
     *
     * @return \Slick\Di\ContainerInterface
     */
    private function container()
    {
        if (!$this->container) {
            $this->container = ContainerFactory::create($this)->container();
        }
        return $this->container;
    }

    /**
     * Creates the index.php file
     *
     * @param AskForNamespace\NameSpaceEntry $nameSpace
     * @param string $webRoot
     */
    protected function createIndexFile(
        AskForNamespace\NameSpaceEntry $nameSpace,
        $webRoot
    )
    {
        /** @var CreateIndexFile $createIndex */
        $createIndex = $this->container()->make(
            CreateIndexFile::class,
            $nameSpace,
            $webRoot,
            $this->input->getArgument('path'),
            '@local.filesystem',
            '@template.engine'
        );
        $createIndex->execute($this->input, $this->output);
    }

    /**
     * Creates the services file
     *
     * @param AskForNamespace\NameSpaceEntry $nameSpace
     */
    protected function createServicesFile(
        AskForNamespace\NameSpaceEntry $nameSpace
    )
    {
        /** @var CreateServicesFile $createServices */
        $createServices = $this->container()->make(
            CreateServicesFile::class,
            $nameSpace,
            $this->input->getArgument('path'),
            '@template.engine',
            '@local.filesystem'
        );

        $createServices->execute($this->input, $this->output);
    }

    /**
     *
     *
     * @param $nameSpace
     */
    protected function createRoutesFile($nameSpace)
    {
        /** @var CreateRoutesFile $createRoutes */
        $createRoutes = $this->container()->make(
            CreateRoutesFile::class,
            $nameSpace,
            $this->input->getArgument('path'),
            '@template.engine',
            '@local.filesystem'
        );
        $createRoutes->execute($this->input, $this->output);
    }

    /**
     *
     *
     * @param $nameSpace
     */
    protected function createController($nameSpace)
    {
        $createController = $this->container()->make(
            CreatePagesController::class,
            $nameSpace,
            $this->input->getArgument('path'),
            '@template.engine',
            '@local.filesystem'
        );
        $createController->execute($this->input, $this->output);
    }
}