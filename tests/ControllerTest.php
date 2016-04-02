<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\PhpEnvironment\Response;
use Slick\Mvc\Controller;
use Slick\Mvc\ControllerInterface;

/**
 * Controller Test case
 *
 * @package Slick\Tests\Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ControllerTest extends TestCase
{
    /**
     * @var Controller|MockObject
     */
    protected $controller;

    /**
     * Sets the SUT controller object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->controller = $this->getMockForAbstractClass(Controller::class);
        $this->controller->register(new Request(), new Response());
    }

    /**
     * Clears for next test
     */
    protected function tearDown()
    {
        $this->controller = null;
        parent::tearDown();
    }

    /**
     * Should return the response object
     * @test
     */
    public function getResponse()
    {
        $this->assertInstanceOf(
            Response::class,
            $this->controller->getResponse()
        );
    }

    /**
     * Should write an attribute in the request object
     * @test
     */
    public function setSingleValue()
    {
        $index = ControllerInterface::REQUEST_ATTR_VIEW_DATA;
        $this->controller->set('foo', 'bar');
        $request = $this->controller->getRequest();
        $this->assertEquals(
            'bar',
            $request->getAttribute($index)['foo']
        );
    }

    /**
     * Should add every value in the viewVars attribute on the request
     * @test
     */
    public function setMultipleValues()
    {
        $foo = 'bar';
        $baz = 'moo';
        $index = ControllerInterface::REQUEST_ATTR_VIEW_DATA;
        $expected = ['foo' => 'bar', 'baz' => 'moo'];
        $this->controller->set(compact('foo', 'baz'));
        $this->assertEquals(
            $expected,
            $this->controller->getRequest()->getAttribute($index)
        );
    }
}
