<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Console\MetaDataGenerator;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Mvc\Console\MetaDataGenerator\Composer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Composer meta data generator test case
 *
 * @package Slick\Tests\Mvc\Console\MetaDataGenerator
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
class ComposerTest extends TestCase
{
    /**
     * @var Composer
     */
    protected $generator;

    /**
     * Sets the SUT composer data generator
     */
    protected function setUp()
    {
        parent::setUp();
        $this->generator = new Composer(__DIR__.'/test-composer.json');
    }

    /**
     * Should grab the project name
     * @test
     */
    public function getProjectName()
    {
        $this->assertEquals(
            'slick/mvc',
            $this->generator->getData()['project']
        );
    }

    /**
     * Should grab the author name
     * @test
     */
    public function getAuthorName()
    {
        $this->assertEquals(
            'Filipe Silva',
            $this->generator->getData()['authorName']
        );
    }

    /**
     * Should grab the author e-mail
     * @test
     */
    public function getAuthorEmail()
    {
        $this->assertEquals(
            'silvam.filipe@gmail.com',
            $this->generator->getData()['authorEmail']
        );
    }

    /**
     * Should throw an exception if provided file is not found
     * @test
     * @expectedException \Slick\Mvc\Exception\FileNotFoundException
     */
    public function fileNotFound()
    {
        new Composer('_test_.json');
    }

    /**
     * Should throw an exception if an error occurs when parsing json file
     * @test
     * @expectedException \Slick\Mvc\Exception\Console\ComposerParseException
     */
    public function parseError()
    {
        $generator = new Composer(__DIR__.'/bad.json');
        $generator->getData();
    }

    /**
     * Should look for composer file if none is given
     * @test
     */
    public function testDefaultComposer()
    {
        $generator = new Composer();
        $this->assertEquals(
            getcwd().'/composer.json',
            $generator->getComposerFile()
        );
    }

    /**
     * Should ask user for name and e-mail if none is present on composer.json
     * @test
     */
    public function requestNameAndEmail()
    {
        $generator = new Composer(__DIR__.'/empty-author.json');
        $generator->setInput($this->getInputMock())
            ->setOutput($this->getOutputMock())
            ->setCommand($this->getCommandMock());
        /** @var QuestionHelper|MockObject $helper */
        $helper = $generator->getQuestionHelper();
        $helper->expects($this->atLeast(2))
            ->method('ask')
            ->willReturnOnConsecutiveCalls(
                'Filipe Silva',
                'silvam.filipe@gmail.com'
            );
        $this->assertEquals(
            [
                'authorName' => 'Filipe Silva',
                'authorEmail' => 'silvam.filipe@gmail.com',
                'project' => 'slick/mvc'
            ],
            $generator->getData()
        );
    }

    /**
     * Should ask user to choose from a list of available authors
     * @test
     */
    public function selectFromMultipleAuthors()
    {
        $generator = new Composer(__DIR__.'/multi-author.json');
        $generator->setInput($this->getInputMock())
            ->setOutput($this->getOutputMock())
            ->setCommand($this->getCommandMock());
        /** @var QuestionHelper|MockObject $helper */
        $helper = $generator->getQuestionHelper();
        $helper->expects($this->once())
            ->method('ask')
            ->willReturn('Filipe Silva <silvam.filipe@gmail.com>');
        $this->assertEquals(
            [
                'authorName' => 'Filipe Silva',
                'authorEmail' => 'silvam.filipe@gmail.com',
                'project' => 'slick/mvc'
            ],
            $generator->getData()
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
