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
        $this->getFlashMessages()->set(
            FlashMessages::TYPE_INFO,
            $message
        );
        return $this;
    }

    /**
     * Add a warning flash message
     *
     * @param string $message
     * @return self
     */
    public function addWarningMessage($message)
    {
        $this->getFlashMessages()->set(
            FlashMessages::TYPE_WARNING,
            $message
        );
        return $this;
    }

    /**
     * Add an error flash message
     *
     * @param string $message
     * @return self
     */
    public function addErrorMessage($message)
    {
        $this->getFlashMessages()->set(
            FlashMessages::TYPE_ERROR,
            $message
        );
        return $this;
    }

    /**
     * Add a success flash message
     *
     * @param string $message
     * @return self
     */
    public function addSuccessMessage($message)
    {
        $this->getFlashMessages()->set(
            FlashMessages::TYPE_SUCCESS,
            $message
        );
        return $this;
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