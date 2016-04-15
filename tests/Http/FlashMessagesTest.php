<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Http;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Slick\Http\SessionDriverInterface;
use Slick\Mvc\Http\FlashMessages;

/**
 * FlashMessages test case
 *
 * @package Slick\Tests\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class FlashMessagesTest extends TestCase
{

    /**
     * @var FlashMessages
     */
    protected $flashMessages;

    protected function setUp()
    {
        parent::setUp();
        $this->flashMessages = new FlashMessages();
    }

    /**
     * @test
     */
    public function setAMessage()
    {
        $type = FlashMessages::TYPE_INFO;
        $message = 'test';
        $driver = $this->getSessionDriverMock();
        $driver->expects($this->once())
            ->method('set')
            ->with('_messages_', [$type => [$message]])
            ->willReturn($this->returnSelf());
        $this->flashMessages->setSessionDriver($driver);
        $obj = $this->flashMessages->set(8, $message);
        $this->assertSame($this->flashMessages, $obj);
    }

    /**
     * @test
     */
    public function getMessages()
    {
        $messages = [
            FlashMessages::TYPE_ERROR => [
                'Just a test message'
            ]
        ];
        $driver = $this->getSessionDriverMock();
        $driver->expects($this->once())
            ->method('get')
            ->with('_messages_', [])
            ->willReturn($messages);
        $this->flashMessages->setSessionDriver($driver);
        $this->assertEquals($messages, $this->flashMessages->get());
    }

    /**
     * Get session driver mocked
     * @return MockObject|SessionDriverInterface
     */
    protected function getSessionDriverMock()
    {
        $class = SessionDriverInterface::class;
        $methods = get_class_methods($class);
        /** @var MockObject|SessionDriverInterface $driver */
        $driver = $this->getMockBuilder($class)
            ->setMethods($methods)
            ->getMock();
        return $driver;
    }
}
