<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http;

use Prophecy\Argument;
use Prophecy\Exception\Prediction\FailedPredictionException;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\WebStack\Infrastructure\Http\FlashMessage\FlashMessageType;
use Slick\WebStack\Infrastructure\Http\FlashMessageInterface;
use Slick\WebStack\Infrastructure\Http\FlashMessages;
use PHPUnit\Framework\TestCase;
use Slick\WebStack\Infrastructure\Http\FlashMessageStorage;

class FlashMessagesTest extends TestCase
{

    use FlashMessages;
    use ProphecyTrait;

    public function testSuccess(): void
    {
        $storage = $this->prophesize(FlashMessageStorage::class);
        $storage->addMessage(Argument::that($this->verifyMessageType(FlashMessageType::SUCCESS)))
            ->shouldBeCalled()->willReturn($storage);
        $this->withMessagesStorage($storage->reveal());
        $this->success('Test message');
    }

    public function testInfo(): void
    {
        $storage = $this->prophesize(FlashMessageStorage::class);
        $storage->addMessage(Argument::that($this->verifyMessageType(FlashMessageType::INFO)))
            ->shouldBeCalled()->willReturn($storage);
        $this->withMessagesStorage($storage->reveal());
        $this->info('Test message');
    }

    public function testWarning(): void
    {
        $storage = $this->prophesize(FlashMessageStorage::class);
        $storage->addMessage(Argument::that($this->verifyMessageType(FlashMessageType::WARNING)))
            ->shouldBeCalled()->willReturn($storage);
        $this->withMessagesStorage($storage->reveal());
        $this->warning('Test message');
    }

    public function testError(): void
    {
        $storage = $this->prophesize(FlashMessageStorage::class);
        $storage->addMessage(Argument::that($this->verifyMessageType(FlashMessageType::ERROR)))
            ->shouldBeCalled()->willReturn($storage);
        $this->withMessagesStorage($storage->reveal());
        $this->error('Test message');
    }

    private function verifyMessageType(FlashMessageType $type): callable
    {
        return function (FlashMessageInterface $message) use ($type) {
            if ($message->type() == $type) {
                return true;
            }

            throw new FailedPredictionException("Message is not of type $type->value, it's {$message->type()->value}");
        };
    }
}
