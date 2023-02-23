<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Dispatcher;

use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
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

    private ReflectionClass $controllerClass;
    private ReflectionMethod $method;

    /**
     * Creates a Controller Dispatch
     *
     * @param string $controllerClassName
     * @param string $method
     * @param array|null $arguments
     *
     * @throws ReflectionException
     */
    public function __construct(string $controllerClassName, string $method, private ?array $arguments = [])
    {
        $this->checkClassExists($controllerClassName);
        $this->checkClassIsAController($controllerClassName);
        $this->controllerClass = new ReflectionClass($controllerClassName);

        $this->checkMethodExists($method);
        $this->method = $this->controllerClass->getMethod($method);
    }

    /**
     * Controller class name
     *
     * @return ReflectionClass
     */
    public function controllerClass(): ReflectionClass
    {
        return $this->controllerClass;
    }

    /**
     * The method name that will be called to handle the request
     *
     * @return ReflectionMethod
     */
    public function handlerMethod(): ReflectionMethod
    {
        return $this->method;
    }

    /**
     * A list of optional arguments to use when calling the request handler
     *
     * @return array
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * Check if controller class exists
     *
     * @param string $controllerClassName
     *
     * @throws ControllerNotFoundException if controller class name does ot exits
     */
    private function checkClassExists(string $controllerClassName): void
    {
        if (! class_exists($controllerClassName)) {
            throw new ControllerNotFoundException(
                "The controller class $controllerClassName does not exists or cannot be loaded."
            );
        }
    }

    /**
     * Check if controller class implements the ControllerInterface
     *
     * @param string $controllerClassName
     *
     * @throws BadControllerClassException If the provided class does not implement ControllerInterface
     */
    private function checkClassIsAController(string $controllerClassName): void
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
    private function checkMethodExists(string $method): void
    {
        if (! $this->controllerClass->hasMethod($method)) {
            throw new MissingControllerMethodException(
                sprintf(
                    "Method %s::%s() is not defined in controller.",
                    $this->controllerClass->getShortName(),
                    $method
                )
            );
        }
    }
}
