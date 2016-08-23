<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\Command\Task;

use Slick\Common\Base;
use Slick\Mvc\Console\Command\TaskInterface;
use Slick\Mvc\Console\MetaDataGenerator\Composer;
use Slick\Mvc\Console\MetaDataGenerator\ConsoleAwareMethods;
use Slick\Mvc\Console\MetaDataGenerator\Controller;
use Slick\Template\Template;
use Slick\Template\TemplateEngineInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Class CreateController
 *
 * @package Slick\Mvc\Console\Command\Task
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property string $controllerName
 * @property string $namespace
 * @property string $sourcePath
 * @property string $basePath
 */
class CreateController extends Base implements TaskInterface
{
    /**
     * @readwrite
     * @var string
     */
    protected $entityName;

    /**
     * @readwrite
     * @var string
     */
    protected $controllerName;

    /**
     * @readwrite
     * @var string
     */
    protected $namespace;

    /**
     * @readwrite
     * @var string
     */
    protected $sourcePath;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var string
     */
    protected $controllerFile;

    /**
     * For input/output getters and setters
     */
    use ConsoleAwareMethods;

    /**
     * @var Controller
     */
    protected $controllerMetaData;

    /**
     * @var TemplateEngineInterface
     */
    protected $templateEngine;

    /**
     * @var QuestionHelper
     */
    protected $questionHelper;

    protected $template = 'controller.twig';

    /**
     * Runs this task
     *
     * @return boolean
     */
    public function run()
    {
        if (!$this->overrideFile()) {
            $this->output->writeln("<info>File was skipped. The controller was not created.</info>");
            return true;
        }
        $data = $this->getControllerMetaData()->getData();
        $data = array_merge(
            $data,
            [
                'controllerName' => $this->controllerName,
                'namespace' => $this->namespace,
                'basePath' => $this->getBasePath()
            ]
        );
        $content = $this->getTemplateEngine()
            ->parse($this->template)
            ->process($data);
        file_put_contents($this->getControllerFile(), $content);
        return true;
    }

    /**
     * Gets controllerMetaData property
     *
     * @return Controller
     */
    public function getControllerMetaData()
    {
        if (null == $this->controllerMetaData) {
            $controller = new Controller();
            $this->configureController($controller);
            $this->setControllerMetaData($controller);
        }
        return $this->controllerMetaData;
    }

    /**
     * Sets controllerMetaData property
     *
     * @param Controller $controllerMetaData
     *
     * @return CreateController
     */
    public function setControllerMetaData(Controller $controllerMetaData)
    {
        $this->controllerMetaData = $controllerMetaData;
        return $this;
    }

    /**
     * Gets templateEngine property
     *
     * @return TemplateEngineInterface
     */
    public function getTemplateEngine()
    {
        if (null == $this->templateEngine) {
            Template::addPath(dirname(dirname(__DIR__)).'/templates');
            $template = (new Template())->initialize();
            $this->setTemplateEngine($template);
        }
        return $this->templateEngine;
    }

    /**
     * Sets templateEngine property
     *
     * @param TemplateEngineInterface $templateEngine
     *
     * @return CreateController
     */
    public function setTemplateEngine(TemplateEngineInterface $templateEngine)
    {
        $this->templateEngine = $templateEngine;
        return $this;
    }

    /**
     * Gets basePath property
     *
     * @return string
     */
    public function getBasePath()
    {
        if (null == $this->basePath) {
            $this->basePath = strtolower($this->controllerName);
        }
        return $this->basePath;
    }

    /**
     * Gets controllerFile property
     *
     * @return string
     */
    public function getControllerFile()
    {
        if (null == $this->controllerFile) {
            $this->setControllerFile(
                "{$this->sourcePath}/{$this->namespace}/" .
                "{$this->controllerName}.php"
            );
        }
        return $this->controllerFile;
    }

    /**
     * Sets controllerFile property
     *
     * @param string $controllerFile
     *
     * @return CreateController
     */
    public function setControllerFile($controllerFile)
    {
        $this->controllerFile = $controllerFile;
        return $this;
    }

    /**
     * Gets questionHelper property
     *
     * @return QuestionHelper
     */
    public function getQuestionHelper()
    {
        if (null == $this->questionHelper) {
            $this->setQuestionHelper($this->getCommand()->getHelper('question'));
        }
        return $this->questionHelper;
    }

    /**
     * Sets questionHelper property
     *
     * @param QuestionHelper $questionHelper
     *
     * @return CreateController
     */
    public function setQuestionHelper(QuestionHelper $questionHelper)
    {
        $this->questionHelper = $questionHelper;
        return $this;
    }

    /**
     * Configures the controller generator
     *
     * @param Controller $controller
     */
    protected function configureController(Controller $controller)
    {
        $controller->setInput($this->getInput())
            ->setOutput($this->getOutput())
            ->setCommand($this->getCommand());
        $composer = new Composer();
        $controller->add($composer);
    }

    /**
     * Check controller file existence and ask if can be overridden
     *
     * @return bool
     */
    protected function overrideFile()
    {
        if (!is_file($this->getControllerFile())) {
            return true;
        }

        $question = new ConfirmationQuestion(
            "\nThe file <comment>{$this->controllerFile}</comment> already " .
            "exists. Override it (y,N)? ",
            false,
            '/^(y|yes)/i'
        );
        return (boolean) $this->getQuestionHelper()->ask(
            $this->input,
            $this->output,
            $question
        );
    }
}