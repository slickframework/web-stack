<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\Command;

use Slick\Mvc\Console\Command\Task\CreateController;
use Slick\Mvc\Console\Command\Task\CreateCrudController;
use Slick\Mvc\Exception\FileNotFoundException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate:controller command
 *
 * @package Slick\Mvc\Console\Command
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class GenerateController extends Command
{

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string|null
     */
    protected $entityName;

    /**
     * @var CreateController
     */
    protected $controllerGenerator;

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this
            ->setName("generate:controller")
            ->setDescription("Generate a controller class file.")
            ->addArgument(
                'controllerName',
                InputArgument::REQUIRED,
                'Controller class name.'
            )
            ->addOption(
                'entity-name',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Creates a CRUD controller for provided entity.'
            )
            ->addOption(
                'source-path',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Sets the application source path',
                getcwd().'/src'
            )
            ->addOption(
                'name-space',
                'o',
                InputOption::VALUE_OPTIONAL,
                'The controller namespace.',
                'Controller'
            )
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
     * @return null|integer null or 0 if everything went fine, or an error code
     *
     * @throws \LogicException When this abstract method is not implemented
     * @see    setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setInput($input)->setOutput($output);
        $output->writeln('Slick MVC <info>v1.2.0</info>');
        $result = $this->getControllerGenerator()
            ->setInput($input)
            ->setOutput($output)
            ->setCommand($this)
            ->run();
        $output->writeln('<info>...Done!</info>');
        return $result;
    }

    /**
     * Gets controllerName property
     *
     * @return string
     */
    public function getControllerName()
    {
        if (null == $this->controllerName) {
            $this->controllerName = ucfirst($this->input->getArgument('controllerName'));
        }
        return $this->controllerName;
    }

    /**
     * Gets path property
     *
     * @return string
     */
    public function getPath()
    {
        if (null == $this->path) {
            $this->path = $this->input->getOption('source-path');
            if (!is_dir($this->path)) {
                throw new FileNotFoundException(
                    "The provided path was not found in your system."
                );
            }
        }
        return $this->path;
    }

    /**
     * Get controller namespace
     *
     * @return string
     */
    public function getNameSpace()
    {
        if (null == $this->namespace) {
            $this->namespace = $this->input->getOption('name-space');
        }
        return $this->namespace;
    }

    /**
     * Get the entity name
     *
     * @return null|string
     */
    public function getEntityName()
    {
        if (null == $this->entityName) {
            $this->entityName = $this->input->getOption('entity-name');
        }
        return $this->entityName;
    }

    /**
     * Gets controllerGenerator property
     *
     * @return CreateController
     */
    public function getControllerGenerator()
    {
        if (null == $this->controllerGenerator) {
            $class = $this->getTaskClass();
            $this->setControllerGenerator(
                new $class([
                    'entityName' => $this->getEntityName(),
                    'controllerName' => $this->getControllerName(),
                    'sourcePath' => $this->getPath(),
                    'namespace' => $this->getNameSpace()
                ])
            );
        }
        return $this->controllerGenerator;
    }

    /**
     * Sets controllerGenerator property
     *
     * @param CreateController $controllerGenerator
     *
     * @return GenerateController
     */
    public function setControllerGenerator(
        CreateController $controllerGenerator
    ) {
        $this->controllerGenerator = $controllerGenerator;
        return $this;
    }

    /**
     * Sets input property
     *
     * @param InputInterface $input
     *
     * @return GenerateController
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * Sets output property
     *
     * @param OutputInterface $output
     *
     * @return GenerateController
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * Gets the task for this command
     *
     * @return string
     */
    protected function getTaskClass()
    {
        $info = "Generate controller '{$this->getControllerName()}'...";
        $class = CreateController::class;
        if (null !== $this->getEntityName()) {
            $info = "Generate CRUD controller '{$this->getControllerName()}'...";
            $class = CreateCrudController::class;
        }
        $this->output->writeln("<info>{$info}</info>");
        return $class;
    }

}