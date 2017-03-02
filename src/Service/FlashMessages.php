<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service;

use Slick\Filter\StaticFilter;
use Slick\Http\SessionDriverInterface;

/**
 * FlashMessages
 *
 * @package Slick\WebStack\Service
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class FlashMessages
{
    /**
     * @var SessionDriverInterface
     */
    private $sessionDriver;

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
        $types = [
            self::TYPE_ERROR, self::TYPE_INFO, self::TYPE_SUCCESS,
            self::TYPE_WARNING
        ];
        if (!in_array($type, $types)) {
            $type = self::TYPE_INFO;
        }
        self::$messages[$type][] = $message;
        $this->sessionDriver->set('_messages_', self::$messages);
        return $this;
    }

    /**
     * clears all messages
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
     * Retrieve all messages and flushes them all
     *
     * @return array
     */
    public function messages()
    {
        $messages = $this->sessionDriver->get('_messages_', []);
        $this->flush();
        return $messages;
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