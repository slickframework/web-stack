<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Dispatcher;

use Slick\WebStack\ControllerInterface;
use Slick\WebStack\Exception\BadControllerClassException;
use Slick\WebStack\Exception\ControllerNotFoundException;
use Slick\WebStack\Exception\MissingControllerMethodException;

/**
 * ControllerDispatch
 *
 * @package Slick\WebStack\Dispatcher
 */
class ControllerDispatch
{
    /**
     * @var \ReflectionClass
     */
    private $controllerClass;

    /**
     * @var \ReflectionMethod
     */
    private $method;

    /**
     * @var array
     */
    private $arguments;

    /**
     * Creates a Controller Dispatch
     *
     * @param string $controllerClassName
     * @param string $method
     * @param array  $arguments
     *
     * @throws ControllerNotFoundException      If controller class name does ot exits
     * @throws BadControllerClassException      If the provided class does not implement ControllerInterface
     * @throws MissingControllerMethodException If provided method is not defined within the class
     */
    public function __construct($controllerClassName, $method, array $arguments = [])
    {
        $this->checkClassExists($controllerClassName);
        $this->checkClassIsAController($controllerClassName);
        $this->controllerClass = new \ReflectionClass($controllerClassName);

        $this->checkMethodExists($method);
        $this->method = $this->controllerClass->getMethod($method);

        $this->arguments = $arguments;
    }

    /**
     * Controller class name
     *
     * @return \ReflectionClass
     */
    public function controllerClass()
    {
        return $this->controllerClass;
    }

    /**
     * The method name that will be called to handle the request
     *
     * @return \ReflectionMethod
     */
    public function handlerMethod()
    {
        return $this->method;
    }

    /**
     * A list of optional arguments to use when calling the request handler
     *
     * @return array
     */
    public function arguments()
    {
        return $this->arguments;
    }

    /**
     * Check if controller class exists
     *
     * @param $controllerClassName
     *
     * @throws ControllerNotFoundException if controller class name does ot exits
     */
    private function checkClassExists($controllerClassName)
    {
        if (! class_exists($controllerClassName)) {
            throw new ControllerNotFoundException(
                "The controller class {$controllerClassName} does not exists or cannot be loaded."
            );
        }
    }

    /**
     * Check if controller class implements the ControllerInterface
     *
     * @param $controllerClassName
     *
     * @throws BadControllerClassException If the provided class does not implement ControllerInterface
     */
    private function checkClassIsAController($controllerClassName)
    {
        if (! is_subclass_of($controllerClassName, ControllerInterface::class)) {
            throw new BadControllerClassException(
                sprintf(
                    "The provided class cannot be used as a controller. ".
                    "It must implement the %s interface.",
                    ControllerInterface::class
                )
            );
        }
    }

    /**
     * Check if controller class has the provided method name
     *
     * @param string $method
     *
     * @throws MissingControllerMethodException If provided method is not defined within the class
     */
    private function checkMethodExists($method)
    {
        if (! $this->controllerClass->hasMethod($method)) {
            throw new MissingControllerMethodException(
                sprintf(
                    "Method '%s' not found in controller %s.",
                    $method,
                    $this->controllerClass->getShortName()
                )
            );
        }
    }
}
