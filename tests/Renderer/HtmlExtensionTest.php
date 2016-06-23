<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Renderer;

use Interop\Container\ContainerInterface;
use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\Mvc\Application;
use Slick\Mvc\Renderer\HtmlExtension;

/**
 * Html Extension Test Case
 *
 * @package Slick\Tests\Mvc\Renderer
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class HtmlExtensionTest extends TestCase
{

    /**
     * @var HtmlExtension
     */
    private $extension;

    protected function setUp()
    {
        parent::setUp();
        $this->extension = new HtmlExtension();
        $this->extension->setRequest(new Request());
        Application::setContainer($this->getContainer());
    }
    
    public function testGetName()
    {
        $this->assertEquals(
            'HTML helper extension',
            $this->extension->getName()
        );
    }

    /**
     * Get URL string
     * @test
     */
    public function getFunctions()
    {
        /** @var \Twig_SimpleFunction[] $functions */
        $functions = $this->extension->getFunctions();
        $names = ['url', 'addCss', 'addJs'];
        foreach ($names as $key => $name) {
            $this->assertEquals($name, $functions[$key]->getName());
        }
    }

    /**
     * Should get the application's container request object if no
     * object is given yet
     * 
     * @test
     */
    public function getApplicationRequest()
    {
        $ext = new HtmlExtension();
        $req = $ext->getRequest();
        $this->assertInstanceOf(ServerRequestInterface::class, $req);
    }


    /**
     * Should create a javacript HTML tag with provided file name and path
     * @test
     */
    public function addJsMacro()
    {
        $expected = '<script src="some/path/test.js"></script>';
        $htmlTag = $this->extension->addJs('test.js', 'some/path');
        $this->assertEquals($expected, $htmlTag);
    }

    /**
     * Should create a css link HTML tag with provided file name and path
     * @test
     */
    public function addCss()
    {
        $expected = '<link href="some/path/test.css" rel="stylesheet" foo="bar">';
        $htmlTag = $this->extension->addCss(
            'test.css',
            'some/path',
            ['foo' => 'bar']
        );
        $this->assertEquals($expected, $htmlTag);
    }

    protected function getContainer()
    {
        $class = ContainerInterface::class;
        $methods = get_class_methods($class);
        $container = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        $container->method('get')
            ->with('request')
            ->willReturn(new Request());
        return $container;
    }
}
