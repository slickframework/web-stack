<?php
/**
 * This file is part of Slick/Mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Console\Command;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Mvc\Console\Command\GenerateController;
use Slick\Mvc\Console\Command\Task\CreateController;
use Slick\Mvc\Console\Command\Task\CreatedCrudController;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * GenerateController command test case
 *
 * @package Slick\Tests\Mvc\Console\Command
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
class GenerateControllerTest extends TestCase
{

    /**
     * @var GenerateController
     */
    protected $command;

    /**
     * Set the SUT command object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->command = new GenerateController();
    }

    /**
     * @test
     */
    public function getCommandHelp()
    {
        $name = $this->command->getName();
        $this->assertEquals('generate:controller', $name);
    }

    /**
     * Should run execute on generate controller
     * @test
     */
    public function executeCommand()
    {
        $input = $this->getInputMock();
        $output = $this->getMock(OutputInterface::class);
        $generator = $this->getCreateControllerMock();
        $generator->expects($this->once())
            ->method('run')
            ->willReturn(true);
        $generator->method('setInput')->willReturn($generator);
        $generator->method('setOutput')->willReturn($generator);
        $generator->method('setCommand')->willReturn($generator);
        $this->command->setControllerGenerator($generator);
        $this->command->run($input, $output);
    }

    /**
     * Should create a CRUD controller creator task
     * @test
     */
    public function getCrudControllerCreator()
    {
        $input = $this->getInputData();
        $output = $this->getMock(OutputInterface::class);
        $this->command->setInput($input)->setOutput($output);
        $creator = $this->command->getControllerGenerator();
        $this->assertInstanceOf(CreatedCrudController::class, $creator);
    }

    /**
     * Should create a controller creator task
     * @test
     */
    public function getSimpleControllerCreator()
    {
        $input = $this->getInputData(['entity-name' => null]);
        $output = $this->getMock(OutputInterface::class);
        $this->command->setInput($input)->setOutput($output);
        $creator = $this->command->getControllerGenerator();
        $this->assertInstanceOf(CreateController::class, $creator);
    }

    /**
     * Should raise an exception if provided path is not found in the system
     * @test
     * @expectedException \Slick\Mvc\Exception\FileNotFoundException
     */
    public function getInvalidPath()
    {
        $input = $this->getInputData(['source-path' => '/some/where/in/this/system']);
        $output = $this->getMock(OutputInterface::class);
        $this->command
            ->setInput($input)
            ->setOutput($output)
            ->getControllerGenerator();
    }

    /**
     * Get input object with default options
     *
     * @param array $data
     * @param string $controllerName
     *
     * @return MockObject|InputInterface
     */
    protected function getInputData($data = [], $controllerName = 'posts')
    {
        $input = $this->getInputMock();
        $data = array_merge(
            [
                'source-path' => getcwd(),
                'name-space' => 'Controller',
                'entity-name' => 'Slick\Tests\Mvc\Fixtures\Domain\Post'
            ],
            $data
        );
        $input->expects($this->atLeastOnce())
            ->method('getOption')
            ->with($this->isType('string'))
            ->willReturnCallback(function($name) use ($data) {
                return $data[$name];
            });
        $input->method('getArgument')
            ->with('controllerName')
            ->willReturn($controllerName);
        return $input;
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
     * Get Create Controller task mock object
     *
     * @return MockObject|CreateController
     */
    protected function getCreateControllerMock()
    {
        $class = CreateController::class;
        $methods = get_class_methods($class);
        /** @var MockObject|CreateController $generator */
        $generator = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $generator;
    }
}
