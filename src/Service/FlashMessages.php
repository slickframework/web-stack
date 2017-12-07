<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service;

use Slick\Http\Session\SessionDriverInterface;

/**
 * FlashMessages
 *
 * @package Slick\WebStack\Service
 */
class FlashMessages
{
    /**
     * @var SessionDriverInterface
     */
    private $sessionDriver;

    /**
     * @var string[]
     */
    private $types = [
        self::TYPE_ERROR, self::TYPE_INFO, self::TYPE_SUCCESS,
        self::TYPE_WARNING
    ];

    /**#@+
     * @const string TYPE for message type constants
     */
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR   = 'danger';
    const TYPE_WARNING = 'warning';
    const TYPE_INFO    = 'info';
    /**#@-*/

    /**
     * @var array
     */
    private static $messages = [];

    /**
     * Creates a flash messages service
     *
     * @param SessionDriverInterface $sessionDriver
     */
    public function __construct(SessionDriverInterface $sessionDriver)
    {
        $this->sessionDriver = $sessionDriver;
        self::$messages = $sessionDriver->get('_messages_', []);
    }

    /**
     * Set a flash message of a give type
     *
     * @param int    $type
     * @param string $message
     *
     * @return FlashMessages
     */
    public function set($type, $message)
    {

        $type = in_array($type, $this->types) ? $type : self::TYPE_INFO;

        self::$messages[$type][] = $message;
        $this->sessionDriver->set('_messages_', self::$messages);
        return $this;
    }

    /**
     * Retrieve all messages and flushes them all
     *
     * @return array
     */
    public function messages()
    {
        $messages = self::$messages;
        $this->flush();
        return $messages;
    }

    /**
     * Clears all messages
     *
     * @return FlashMessages
     */
    public function flush()
    {
        self::$messages = [];
        $this->sessionDriver->erase('_messages_');
        return $this;
    }

    /**
     * Add an info flash message
     *
     * @param string $message
     * @return self
     */
    public function addInfo($message)
    {
        return $this->set(FlashMessages::TYPE_INFO, $message);
    }

    /**
     * Add a warning flash message
     *
     * @param string $message
     * @return self
     */
    public function addWarning($message)
    {
        return $this->set(FlashMessages::TYPE_WARNING, $message);
    }

    /**
     * Add an error flash message
     *
     * @param string $message
     * @return self
     */
    public function addError($message)
    {
        return $this->set(FlashMessages::TYPE_ERROR, $message);
    }

    /**
     * Add a success flash message
     *
     * @param string $message
     * @return self
     */
    public function addSuccess($message)
    {
        return $this->set(FlashMessages::TYPE_SUCCESS, $message);
    }
}
