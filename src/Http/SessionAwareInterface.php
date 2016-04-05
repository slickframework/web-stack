<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Http;

use Slick\Http\SessionDriverInterface;

/**
 * HTTP Session Aware Interface
 * 
 * @package Slick\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface SessionAwareInterface
{

    /**
     * Gets Session driver
     * 
     * @return SessionDriverInterface
     */
    public function getSessionDriver();

    /**
     * Sets session driver
     * 
     * @param SessionDriverInterface $driver
     * 
     * @return self|$this
     */
    public function setSessionDriver(SessionDriverInterface $driver);
}