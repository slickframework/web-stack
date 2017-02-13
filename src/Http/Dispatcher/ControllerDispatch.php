<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http\Dispatcher;

/**
 * Controller Dispatch
 *
 * @package Slick\WebStack\Http\Dispatcher
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ControllerDispatch
{

    private $controllerClassName;
    private $method;
    private $arguments;

    /**
     * Creates a Controller Dispatch
     *
     * @param string $controllerClassName
     * @param string $method
     * @param array $arguments
     */
    public function __construct($controllerClassName, $method, array $arguments)
    {
        $this->controllerClassName = $controllerClassName;
        $this->method = $method;
        $this->arguments = $arguments;
    }

    /**
     * Get FQ class name of the controller
     *
     * @return string
     */
    public function getControllerClassName()
    {
        return $this->controllerClassName;
    }

    /**
     * Get controller method to call
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get method arguments
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
