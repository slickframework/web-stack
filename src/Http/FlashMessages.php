<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Http;

use Slick\Filter\StaticFilter;

/**
 * Session Flash Messages
 * 
 * @package Slick\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class FlashMessages
{

    /**#@+
     * @const string TYPE for message type constants
     */
    const TYPE_SUCCESS = 0;
    const TYPE_ERROR   = 1;
    const TYPE_WARNING = 2;
    const TYPE_INFO    = 3;
    /**#@-*/

    /**
     * Uses session
     */
    use SessionAwareMethods;

    /**
     * @var array message type descriptions
     */
    public $classes = [
        self::TYPE_SUCCESS => 'success',
        self::TYPE_WARNING => 'warning',
        self::TYPE_INFO    => 'info',
        self::TYPE_ERROR   => 'danger'
    ];

    /**
     * @var array
     */
    protected static $messages = [];

    /**
     * Set a flash message of a give type
     *
     * @param int $type
     * @param string $message
     *
     * @return self
     */
    public function set($type, $message)
    {
        $type = StaticFilter::filter('number', $type);
        if ($type < 0 || $type > 3) {
            $type = static::TYPE_INFO;
        }
        self::$messages[$type][] = $message;
        $this->getSessionDriver()
            ->set('_messages_', self::$messages)
        ;
        return $this;
    }

    /**
     * Retrieve all messages and flushes them all
     *
     * @return array
     */
    public function get()
    {
        self::$messages = $this->getSessionDriver()->get('_messages_', []);
        $messages = self::$messages;
        $this->flush();
        return $messages;
    }

    /**
     * clears all messages
     *
     * @return FlashMessages
     */
    public function flush()
    {
        self::$messages = [];
        $this->getSessionDriver()
            ->set('_messages_', self::$messages)
        ;
        return $this;
    }
}