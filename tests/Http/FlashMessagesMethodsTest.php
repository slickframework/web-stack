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
use Slick\Mvc\Http\FlashMessages;
use Slick\Mvc\Http\FlashMessagesMethods;

/**
 * Flash Messages Methods trait test case
 *
 * @package Slick\Tests\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>>
 */
class FlashMessagesMethodsTest extends TestCase
{

    use FlashMessagesMethods;

    /**
     * Lazy loads flash messages
     * @test
     */
    public function getFlashMessagesObject()
    {
        $fm = $this->getFlashMessages();
        $this->assertInstanceOf(FlashMessages::class, $fm);
    }

    public function data()
    {
        return [
            'info' => ['addInfoMessage', FlashMessages::TYPE_INFO],
            'warning' => ['addWarningMessage', FlashMessages::TYPE_WARNING],
            'error' => ['addErrorMessage', FlashMessages::TYPE_ERROR],
            'success' => ['addSuccessMessage', FlashMessages::TYPE_SUCCESS],
        ];
    }

    /**
     * @param $method
     * @dataProvider  data
     */
    public function testAddMessage($method, $type)
    {
        $this->setFlashMessages($this->getMockedFlashMessages($type));
        $obj = $this->$method('test');
        $this->assertSame($this, $obj);
    }

    /**
     * @return MockObject|FlashMessages
     * @param int $type
     */
    protected function getMockedFlashMessages($type)
    {
        /** @var FlashMessages|MockObject $obj */
        $obj = $this->getMockBuilder(FlashMessages::class)
            ->setMethods(['set'])
            ->getMock();
        $obj->expects($this->once())
            ->method('set')
            ->with($type, $this->isType('string'))
            ->willReturn($this->returnSelf());
        return $obj;
    }
}
