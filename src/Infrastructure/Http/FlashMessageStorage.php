<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http;

use Slick\Http\Session\SessionDriverInterface;
use Slick\WebStack\Infrastructure\Http\FlashMessage\FlashMessageType;

/**
 * FlashMessageController
 *
 * @package Slick\WebStack\Infrastructure\Http
 */
class FlashMessageStorage
{
    /**
     * @var array<string, array<FlashMessageInterface>>
     */
    private array $flashMessages;

    /**
     * Creates a FlashMessageStorage
     *
     * @param SessionDriverInterface $session A session driver instance.
     */
    public function __construct(private readonly SessionDriverInterface $session)
    {
        $this->flashMessages = unserialize($this->session->get('flash_messages', serialize([])));
    }

    /**
     * Adds a flash message to the array of flash messages.
     *
     * @param FlashMessageInterface $message The flash message to be added.
     *
     * @return self Returns an instance of the current class.
     */
    public function addMessage(FlashMessageInterface $message): self
    {
        $this->flashMessages[$message->type()->value][] = $message;
        $this->saveInSession();
        return $this;
    }

    /**
     * Retrieves the flash messages of a specific type or all the flash messages
     * if no type is specified.
     *
     * @param FlashMessageType|null $type The type of flash messages to retrieve.
     *                                    If null, retrieves all flash messages.
     *
     * @return array<FlashMessageInterface> The array of flash messages.
     */
    public function consume(?FlashMessageType $type = null): array
    {
        if (null === $type) {
            return $this->allTypes();
        }

        return $this->withType($type);
    }

    /**
     * Filters the flash messages by type and returns an array of consumed messages.
     *
     * @param FlashMessageType $type The type of flash messages to filter by.
     *
     * @return array<FlashMessageInterface> Returns an array of consumed flash messages.
     */
    private function withType(FlashMessageType $type): array
    {
        if (array_key_exists($type->value, $this->flashMessages)) {
            $messages = array_map(fn ($message) => $message->consume(), $this->flashMessages[$type->value]);
            unset($this->flashMessages[$type->value]);
            $this->saveInSession();
            return $messages;
        }

        return [];
    }

    /**
     * Retrieves all flash message types and consumes them.
     *
     * @return array<FlashMessageInterface> An array containing the consumed flash messages.
     */
    private function allTypes(): array
    {
        $messages = [];
        array_walk_recursive(
            $this->flashMessages,
            function (FlashMessageInterface $message) use (&$messages) {
                $messages[] = $message->consume();
            }
        );
        $this->flashMessages = [];
        $this->saveInSession();
        return $messages;
    }

    /**
     * Saves the flash messages in the session.
     *
     * @return void
     */
    private function saveInSession(): void
    {
        $this->session->set('flash_messages', serialize($this->flashMessages));
    }
}
