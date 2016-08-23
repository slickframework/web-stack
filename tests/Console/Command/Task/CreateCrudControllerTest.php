<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Console\Command\Task;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Mvc\Console\Command\Task\CreateCrudController;
use Slick\Tests\Mvc\Fixtures\Domain\Post;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * CreatedCrudControllerTest
 *
 * @package Slick\Tests\Mvc\Console\Command\Task
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
class CreateCrudControllerTest extends TestCase
{

    /**
     * @var CreateCrudController
     */
    protected $creator;

    protected function setUp()
    {
        parent::setUp();
        $this->creator = new CreateCrudController(
            [
                'controllerName' => 'Articles',
                'namespace' => 'Controller',
                'sourcePath' => getcwd().'/tests',
                'input' => $this->getInputMock(),
                'output' => $this->getOutputMock(),
                'command' => $this->getCommandMock(),
                'entityName' => Post::class
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
     * Should use the template and render out a file with controller class code
     * @test
     */
    public function createANewFile()
    {
        $engine = $this->creator->getTemplateEngine();
        $expected = $engine->parse('crud-controller.twig')->process([
            'project' => 'slick/mvc',
            'authorName' => 'Filipe Silva',
            'authorEmail' => 'silvam.filipe@gmail.com',
            'controllerName' => 'Articles',
            'namespace' => 'Controller',
            'basePath' => 'articles',
            'entityClassName' => Post::class,
            'entityName' => 'Post',
            'formFilename' => 'post-form',
        ]);
        $this->creator->run();
        $this->assertEquals(
            $expected,
            file_get_contents($this->creator->getControllerFile())
        );
    }

    /**
     * Should raise an exception if the provided name is not from an
     * existing class.
     *
     * @test
     * @expectedException  \Slick\Mvc\Exception\Console\EntityClassNotFound
     */
    public function setInvalidEntityName()
    {
        $this->creator->setEntityName('a_class');
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
