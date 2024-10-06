<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http;

use Slick\Di\Definition\Attributes\Autowire;
use Slick\WebStack\Infrastructure\Http\FlashMessage\FlashMessageType;
use Slick\WebStack\Infrastructure\Http\FlashMessage\Message;
use Test\Slick\WebStack\Infrastructure\Http\FlashMessagesTest;
use Test\Slick\WebStack\Infrastructure\Http\FlashMessageStorageTest;

/**
 * FlashMessages
 *
 * @package Slick\WebStack\Infrastructure\Http
 * @phpstan-ignore trait.unused
 */
trait FlashMessages
{
    protected ?FlashMessageStorage $flashMessageStorage = null;

    /**
     * Sets the flash message storage.
     *
     * @param FlashMessageStorage $flashMessageStorage The flash message storage instance to set.
     * @return FlashMessageStorageTest|FlashMessages|FlashMessagesTest Returns the current object instance.
     */
    #[Autowire]
    public function withMessagesStorage(FlashMessageStorage $flashMessageStorage): self
    {
        $this->flashMessageStorage = $flashMessageStorage;
        return $this;
    }

    /**
     * Adds a success flash message and adds it to the flash message storage.
     *
     * @param string $message The success message to add.
     * @return FlashMessageInterface Returns the created flash message.
     */
    public function success(string $message): FlashMessageInterface
    {
        $flashMessage = new Message($message, FlashMessageType::SUCCESS);
        $this->flashMessageStorage->addMessage($flashMessage);
        return $flashMessage;
    }

    /**
     * Create an info flash message and add it to the storage
     *
     * @param string $message The message to be displayed
     * @return FlashMessageInterface The created flash message
     */
    public function info(string $message): FlashMessageInterface
    {
        $flashMessage = new Message($message, FlashMessageType::INFO);
        $this->flashMessageStorage->addMessage($flashMessage);
        return $flashMessage;
    }

    /**
     * Generates a warning flash message and adds it to the flash message storage.
     *
     * @param string $message The message to be displayed in the warning flash message.
     * @return FlashMessageInterface The generated warning flash message.
     */
    public function warning(string $message): FlashMessageInterface
    {
        $flashMessage = new Message($message, FlashMessageType::WARNING);
        $this->flashMessageStorage->addMessage($flashMessage);
        return $flashMessage;
    }

    /**
     * Generates an error flash message and adds it to the flash message storage.
     *
     * @param string $message The message to be displayed in the error flash message.
     * @return FlashMessageInterface The generated error flash message.
     */
    public function error(string $message): FlashMessageInterface
    {
        $flashErrorMessage = new Message($message, FlashMessageType::ERROR);
        $this->flashMessageStorage->addMessage($flashErrorMessage);
        return $flashErrorMessage;
    }
}
