<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Dispatcher;

use Slick\Di\ContainerInterface;
use Slick\WebStack\Controller\ControllerContextInterface;
use Slick\WebStack\ControllerInterface;

/**
 * ControllerInvoker
 *
 * @package Slick\WebStack\Dispatcher
 */
class ControllerInvoker implements ControllerInvokerInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Creates a Controller Invoker
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Invokes the controller action returning view data
     *
     * @param ControllerContextInterface $context
     * @param ControllerDispatch $dispatch
     *
     * @return array
     */
    public function invoke(
        ControllerContextInterface $context,
        ControllerDispatch $dispatch
    ) {
        /** @var ControllerInterface $controller */
        $controller = $this->createController($dispatch->controllerClass());
        $controller->runWithContext($context);

        $method = $dispatch->handlerMethod();
        $method->invokeArgs($controller, $dispatch->arguments());

        return $controller->data();
    }

    /**
     * Uses the dependency container to create the controller
     *
     * @param \ReflectionClass $controllerName
     *
     * @return ControllerInterface
     */
    private function createController(\ReflectionClass $controllerName)
    {
        $controller = $this->container->make($controllerName->getName());
        return $controller;
    }
}
