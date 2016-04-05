<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Http;

use Slick\Http\SessionDriverInterface;
use Slick\Mvc\Application;

/**
 * Session Aware Interface methods implementation
 * 
 * @package Slick\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait SessionAwareMethods
{

    /**
     * @var SessionDriverInterface
     */
    protected $sessionDriver;

    /**
     * Gets Session driver
     *
     * @return SessionDriverInterface
     */
    public function getSessionDriver()
    {
        if (null == $this->sessionDriver) {
            $this->setSessionDriver(Application::container()->get('session'));
        }
        return $this->sessionDriver;
    }

    /**
     * Sets session driver
     *
     * @param SessionDriverInterface $driver
     *
     * @return self|$this
     */
    public function setSessionDriver(SessionDriverInterface $driver)
    {
        $this->setSessionDriver($driver);
        return $this;
    }
}