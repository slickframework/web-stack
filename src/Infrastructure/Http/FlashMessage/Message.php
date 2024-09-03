<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\FlashMessage;

use Slick\WebStack\Infrastructure\Http\FlashMessageInterface;

/**
 * Message
 *
 * @package Slick\WebStack\Infrastructure\Http\FlashMessage
 */
final class Message implements FlashMessageInterface
{
    private bool $consumed = false;

    public function __construct(
        private readonly string $message,
        private readonly FlashMessageType $type = FlashMessageType::INFO
    ) {
    }

    /**
     * @inheritDoc
     */
    public function type(): FlashMessageType
    {
        return $this->type;
    }

    /**
     * @inheritDoc
     */
    public function message(): string
    {
        return $this->message;
    }

    /**
     * @inheritDoc
     */
    public function wasConsumed(): bool
    {
        return $this->consumed;
    }

    public function consume(): self
    {
        $this->consumed = true;
        return $this;
    }
}
