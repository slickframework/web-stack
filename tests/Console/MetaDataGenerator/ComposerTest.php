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

    public function testDefaultComposer()
    {
        $generator = new Composer();
        $this->assertEquals(
            getcwd().'/composer.json',
            $generator->getComposerFile()
        );
    }
}
