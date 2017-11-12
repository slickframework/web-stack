<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack;

use Slick\WebStack\Controller\ControllerContextInterface;


/**
 * Controller Interface
 *
 * @package Slick\WebStack
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface ControllerInterface
{

    /**
     * Sets te context for this controller execution
     *
     * @param ControllerContextInterface $context
     *
     * @return self|ControllerInterface
     */
    public function runWithContext(ControllerContextInterface $context);

    /**
     * Sets a variable to the view data model
     *
     * If you provide an associative array in the $name argument it will be
     * set all the elements using the key as the variable name on view
     * data model.
     *
     * @param string|array $name
     * @param mixed$value
     *
     * @return self|ControllerInterface
     */
    public function set($name, $value = null);

    /**
     * A view data model used by renderer
     *
     * @return array
     */
    public function data();
}