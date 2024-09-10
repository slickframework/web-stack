<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http;

use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Slick\Http\Session\SessionDriverInterface;
use Slick\WebStack\Infrastructure\Http\FlashMessage\FlashMessageType;
use Slick\WebStack\Infrastructure\Http\FlashMessage\Message;
use Slick\WebStack\Infrastructure\Http\FlashMessageInterface;
use Slick\WebStack\Infrastructure\Http\FlashMessages;
use Slick\WebStack\Infrastructure\Http\FlashMessageStorage;
use PHPUnit\Framework\TestCase;

class FlashMessageStorageTest extends TestCase
{

    use ProphecyTrait;
    use FlashMessages;

    public function testConstruct(): void
    {
        $session = $this->mockSessionDriver();
        $storage = new FlashMessageStorage($session->reveal());
        $this->assertInstanceOf(FlashMessageStorage::class, $storage);
    }

    public function testAddMessage(): void
    {
        $session = $this->mockSessionDriver();
        $storage = new FlashMessageStorage($session->reveal());
        $this->withMessagesStorage($storage);
        $message = $this->success("test");
        $this->assertSame($message, $storage->consume()[0]);
    }

    public function testConsume(): void
    {
        $session = $this->mockSessionDriver();
        $storage = new FlashMessageStorage($session->reveal());
        $this->withMessagesStorage($storage);
        $message = $this->info("test");
        $this->assertSame($message, $storage->consume()[0]);
        $this->assertTrue($message->wasConsumed());
        $this->assertEmpty($storage->consume());
    }

    public function testConsumeType(): void
    {
        $session = $this->mockSessionDriver();
        $storage = new FlashMessageStorage($session->reveal());
        $this->withMessagesStorage($storage);
        $message = $this->error("test");
        $this->assertSame($message, $storage->consume(FlashMessageType::ERROR)[0]);
        $this->assertTrue($message->wasConsumed());
        $this->assertEmpty($storage->consume(FlashMessageType::ERROR));
    }

    public function testSaveToSession(): void
    {
        $messageTxt = "test";
        $session = $this->mockSessionDriver();
        $session->set(
            'flash_messages',
            Argument::that($this->verifySerialization(FlashMessageType::WARNING, $messageTxt))
        )->shouldBeCalled()->willreturn($session);

        $flashMessageStorage = new FlashMessageStorage($session->reveal());
        $this->withMessagesStorage($flashMessageStorage);
        $this->assertInstanceOf(FlashMessageInterface::class, $this->warning($messageTxt));
    }

    /**
     * @return ObjectProphecy|SessionDriverInterface
     */
    private function mockSessionDriver(): SessionDriverInterface|ObjectProphecy
    {
        $session = $this->prophesize(SessionDriverInterface::class);
        $session->get('flash_messages', serialize([]))->willReturn(serialize([]));
        $session->set('flash_messages', Argument::type('string'))->willReturn($session);
        return $session;
    }

    private function verifySerialization(FlashMessageType $type, string $text): callable
    {
        $message = new Message($text, $type);
        $data = serialize([$type->value => [$message]]);
        return function (string $payload) use ($data) {
            $this->assertSame($data, $payload);
            return true;
        };
    }
}
