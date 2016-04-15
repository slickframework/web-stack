<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Http;

/**
 * Flash Message Methods
 * 
 * @package Slick\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait FlashMessagesMethods
{

    /**
     * @var FlashMessages
     */
    protected $flashMessages;

    /**
     * @return FlashMessages
     */
    public function getFlashMessages()
    {
        if (null == $this->flashMessages) {
            $this->setFlashMessages(new FlashMessages());
        }
        return $this->flashMessages;
    }

    /**
     * Set flash messages service
     *
     * @param FlashMessages $flashMessages
     *
     * @return FlashMessagesMethods
     */
    public function setFlashMessages(FlashMessages $flashMessages)
    {
        $this->flashMessages = $flashMessages;
        return $this;
    }

    /**
     * Add an info flash message
     *
     * @param string $message
     * @return self
     */
    public function addInfoMessage($message)
    {
        return $this->setMessage(FlashMessages::TYPE_INFO, $message);
    }

    /**
     * Add a warning flash message
     *
     * @param string $message
     * @return self
     */
    public function addWarningMessage($message)
    {
        return $this->setMessage(FlashMessages::TYPE_WARNING, $message);
    }

    /**
     * Add an error flash message
     *
     * @param string $message
     * @return self
     */
    public function addErrorMessage($message)
    {
        return $this->setMessage(FlashMessages::TYPE_ERROR, $message);
    }

    /**
     * Add a success flash message
     *
     * @param string $message
     * @return self
     */
    public function addSuccessMessage($message)
    {
        return $this->setMessage(FlashMessages::TYPE_SUCCESS, $message);
    }

    /**
     * Sets a flash message to be displayed
     *
     * @param int $type
     * @param string $message
     *
     * @return self
     */
    public function setMessage($type, $message)
    {
        $this->getFlashMessages()->set($type, $message);
        return $this;
    }
}