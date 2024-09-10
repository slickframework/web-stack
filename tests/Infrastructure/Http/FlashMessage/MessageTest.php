<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\FlashMessage;

use Slick\WebStack\Infrastructure\Http\FlashMessage\FlashMessageType;
use Slick\WebStack\Infrastructure\Http\FlashMessage\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{

    public function testWasConsumed(): void
    {
        $msg = new Message('test');
        $this->assertFalse($msg->wasConsumed());
    }

    public function testDefaultType(): void
    {
        $msg = new Message('test');
        $this->assertEquals(FlashMessageType::INFO, $msg->type());
    }

    public function testMessage(): void
    {
        $message = 'test';
        $msg = new Message($message);
        $this->assertEquals($message, $msg->message());
    }

    public function testConsumeMessage(): void
    {
        $msg = new Message('test');
        $this->assertFalse($msg->wasConsumed());
        $this->assertSame($msg, $msg->consume());
        $this->assertTrue($msg->wasConsumed());
    }

    public function testSerialized(): void
    {
        $msg = new Message('test');
        $data = serialize($msg);
        $newMsg = unserialize($data);
        $this->assertEquals($msg, $newMsg);
    }
}
