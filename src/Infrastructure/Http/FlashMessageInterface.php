<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http;

use Slick\WebStack\Infrastructure\Http\FlashMessage\FlashMessageType;

/**
 * FlashMessageInterface
 *
 * @package Slick\WebStack\Infrastructure\Http
 */
interface FlashMessageInterface
{

    /**
     * Returns the type of FlashMessage.
     *
     * @return FlashMessageType The type of FlashMessage.
     */
    public function type(): FlashMessageType;

    /**
     * Returns the message of the FlashMessage.
     *
     * @return string The message of the FlashMessage.
     */
    public function message(): string;

    /**
     * Checks if the FlashMessage was displayed.
     *
     * @return bool True if the FlashMessage was displayed, false otherwise.
     */
    public function wasConsumed(): bool;

    /**
     * Consumes the FlashMessage.
     *
     * @return self The instance of the class.
     */
    public function consume(): self;
}
