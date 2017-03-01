<?php

/**
 * This file is part of WebStack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CommandLine;

use Behat\MinkExtension\Context\MinkContext;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Prophecy\Prophet;
use Slick\Template\Template;
use Slick\WebStack\Console\Command\Task\AskForNamespace\ComposerReader;
use Slick\WebStack\Console\Command\Task\AskForNamespace\NameSpaceEntry;
use Slick\WebStack\Console\Command\Task\CopyTemplateFiles;
use Slick\WebStack\Console\Command\Task\CreateIndexFile;
use Slick\WebStack\Console\Command\Task\CreatePagesController;
use Slick\WebStack\Console\Command\Task\CreateRoutesFile;
use Slick\WebStack\Console\Command\Task\CreateServicesFile;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CommandLineContext
 *
 * @package CommandLine
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class CommandLineContext extends MinkContext
{

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $documentRoot;

    /**
     * @var ComposerReader
     */
    private $composerReader;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var NameSpaceEntry
     */
    private $namespace;

    /**
     * @var \Slick\Template\TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * @var object|InputInterface
     */
    private $input;

    /**
     * @var object|OutputInterface
     */
    private $output;

    public function __construct()
    {
        $composerFile = dirname(dirname(dirname(__DIR__))).'/composer.json';
        $templates = dirname(dirname(dirname(__DIR__))).'/templates';
        $this->composerReader = new ComposerReader($composerFile);
        $this->filesystem = new Filesystem(new Local(getcwd()));
        Template::addPath($templates);
        $this->templateEngine = (new Template())->initialize();
        $prophet = new Prophet();
        $this->input = $prophet->prophesize(InputInterface::class)->reveal();
        $this->output = $prophet->prophesize(OutputInterface::class)->reveal();
    }

    /**
     * @Given I run init command with :path
     *
     * @param string $path
     */
    public function iRunCommandWith($path)
    {
        $this->path = $path;
    }

    /**
     * @Given I set :documentRoot for document root
     *
     * @param string $documentRoot
     */
    public function iSetForDocumentRoot($documentRoot)
    {
        $this->documentRoot = $documentRoot;
    }

    /**
     * @Given I choose :namespace for namespace
     *
     * @param $namespace
     */
    public function iChooseForNamespace($namespace)
    {
        foreach ($this->composerReader->nameSpaces() as $entry) {
            if (trim($entry->getNameSpace(), '\\') == $namespace) {
                $this->namespace = $entry;
                return;
            }
        }

        throw new \InvalidArgumentException(
            "Cannot find any namespace '{$namespace}'"
        );
    }

    /**
     * @When /^I execute the command$/
     */
    public function iExecuteTheCommand()
    {
        $this->createIndexFile();
        $this->createServicesFile();
        $this->createRoutesFile();
        $this->createController();
        $this->copyFiles();
    }

    /**
     * Creates the index.php file
     */
    private function createIndexFile()
    {
        $createIndex = new CreateIndexFile(
            $this->namespace,
            $this->documentRoot,
            $this->path,
            $this->filesystem,
            $this->templateEngine
        );
        $createIndex->execute($this->input, $this->output);
    }

    /**
     * Creates the services file
     */
    private function createServicesFile()
    {
        $createServices = new CreateServicesFile(
            $this->namespace,
            $this->path,
            $this->templateEngine,
            $this->filesystem
        );
        $createServices->execute($this->input, $this->output);
    }

    /**
     * Creates routes file
     */
    private function createRoutesFile()
    {
        $createRoutes = new CreateRoutesFile(
            $this->namespace,
            $this->path,
            $this->templateEngine,
            $this->filesystem
        );
        $createRoutes->execute($this->input, $this->output);
    }

    /**
     * Create controller
     */
    private function createController()
    {
        $createController = new CreatePagesController(
            $this->namespace,
            $this->path,
            $this->templateEngine,
            $this->filesystem
        );
        $createController->execute($this->input, $this->output);
    }

    private function copyFiles()
    {
        $copyFiles = new CopyTemplateFiles(
            $this->namespace,
            $this->path,
            $this->filesystem
        );
        $copyFiles->execute($this->input, $this->output);
    }

}
