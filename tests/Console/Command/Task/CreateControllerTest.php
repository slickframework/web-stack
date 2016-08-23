<?php

/**
 * This file is part of Mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Console\Command\Task;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Mvc\Console\Command\Task\CreateController;
use Slick\Mvc\Console\MetaDataGenerator\Controller;
use Slick\Template\Engine\Twig;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create Controller test case
 *
 * @package Slick\Tests\Mvc\Console\Command\Task
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
class CreateControllerTest extends TestCase
{

    /**
     * @var CreateController
     */
    protected $creator;

    protected function setUp()
    {
        parent::setUp();
        $this->creator = new CreateController(
            [
                'controllerName' => 'Articles',
                'namespace' => 'Controller',
                'sourcePath' => getcwd().'/tests',
                'input' => $this->getInputMock(),
                'output' => $this->getOutputMock(),
                'command' => $this->getCommandMock()
            ]
        );
    }

    /**
     * Deletes any created file
     */
    protected function tearDown()
    {
        if (is_file($this->creator->getControllerFile())) {
            unlink($this->creator->getControllerFile());
        }
        parent::tearDown();
    }

    /**
     * Should create the controller file name based on the source path,
     * namespace and controller name.
     * @test
     */
    public function getControllerFileName()
    {
        $file = getcwd()."/tests/Controller/Articles.php";
        $this->assertEquals($file, $this->creator->getControllerFile());
    }

    /**
     * Should set the base path from controller name
     * @test
     */
    public function getBasePath()
    {
        $this->assertEquals('articles', $this->creator->getBasePath());
    }

    /**
     * Should use twig engine for template usage
     * @test
     */
    public function getEngine()
    {
        $engine = $this->creator->getTemplateEngine();
        $this->assertInstanceOf(Twig::class, $engine);
    }

    /**
     * Should get a configured controller meta data generator
     * @test
     */
    public function getControllerMetaData()
    {
        $controller = $this->creator->getControllerMetaData();
        $this->assertInstanceOf(Controller::class, $controller);
    }

    /**
     * Should gran the helper using the factory method on command object
     * @test
     */
    public function getQuestionHelper()
    {
        /** @var Command|MockObject $command */
        $command = $this->creator->getCommand();
        $command->expects($this->once())
            ->method('getHelper')
            ->with('question')
            ->willReturn($this->getQuestionMock());
        $helper = $this->creator->getQuestionHelper();
        $this->assertInstanceOf(QuestionHelper::class, $helper);
    }

    /**
     * Should use the template and render out a file with controller class code
     * @test
     */
    public function createANewFile()
    {
        $engine = $this->creator->getTemplateEngine();
        $expected = $engine->parse('controller.twig')->process([
            'project' => 'slick/mvc',
            'authorName' => 'Filipe Silva',
            'authorEmail' => 'silvam.filipe@gmail.com',
            'controllerName' => 'Articles',
            'namespace' => 'Controller'
        ]);
        $this->creator->run();
        $this->assertEquals(
            $expected,
            file_get_contents($this->creator->getControllerFile())
        );
    }

    /**
     * Should override the file wen user responds yes
     * @test
     */
    public function overrideExistingFile()
    {
        file_put_contents($this->creator->getControllerFile(), 'Hello world!');
        /** @var QuestionHelper|MockObject $helper */
        $helper = $this->creator->getQuestionHelper();
        $helper->expects($this->once())
            ->method('ask')
            ->willReturn(true);
        $engine = $this->creator->getTemplateEngine();
        $expected = $engine->parse('controller.twig')->process([
            'project' => 'slick/mvc',
            'authorName' => 'Filipe Silva',
            'authorEmail' => 'silvam.filipe@gmail.com',
            'controllerName' => 'Articles',
            'namespace' => 'Controller'
        ]);
        $this->creator->run();
        $this->assertEquals(
            $expected,
            file_get_contents($this->creator->getControllerFile())
        );
    }

    /**
     * Should skip file creation on user request
     * @test
     */
    public function skipFileCreation()
    {
        $expected = 'Hello world!';
        file_put_contents($this->creator->getControllerFile(), $expected);
        /** @var QuestionHelper|MockObject $helper */
        $helper = $this->creator->getQuestionHelper();
        $helper->expects($this->once())
            ->method('ask')
            ->willReturn(false);
        $this->creator->run();
        $this->assertEquals(
            $expected,
            file_get_contents($this->creator->getControllerFile())
        );
    }

    /**
     * Get the input method mock
     *
     * @return MockObject|InputInterface
     */
    protected function getInputMock()
    {
        $class = InputInterface::class;
        $methods = get_class_methods($class);
        /** @var InputInterface|MockObject $input */
        $input = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $input;
    }

    /**
     * Get the out method mock
     *
     * @return MockObject|OutputInterface
     */
    protected function getOutputMock()
    {
        $class = OutputInterface::class;
        $methods = get_class_methods($class);
        /** @var OutputInterface|MockObject $output */
        $output = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $output;
    }

    /**
     * Get the out method mock
     *
     * @return MockObject|Command
     */
    protected function getCommandMock()
    {
        $class = Command::class;
        $methods = get_class_methods($class);
        /** @var Command|MockObject $command */
        $command = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
        $command->method('getHelper')
            ->with('question')
            ->willReturn($this->getQuestionMock());
        return $command;
    }

    /**
     * Get mock for question helper
     *
     * @return MockObject|QuestionHelper
     */
    protected function getQuestionMock()
    {
        $class = QuestionHelper::class;
        $methods = get_class_methods($class);
        /** @var QuestionHelper|MockObject $helper */
        $helper = $this->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
        return $helper;
    }
}
