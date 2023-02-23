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
     * Creates a Controller Invoker
     *
     * @param ContainerInterface $container
     */
    public function __construct(private ContainerInterface $container)
    {
    }

    /**
     * Invokes the controller action returning view data
     *
     * @param ControllerContextInterface $context
     * @param ControllerDispatch $dispatch
     *
     * @return array
     * @throws ReflectionException
     */
    public function invoke(
        ControllerContextInterface $context,
        ControllerDispatch $dispatch
    ): array {
        $controller = $this->createController($dispatch->controllerClass());
        $controller->runWithContext($context);

        $method = $dispatch->handlerMethod();
        $response = $method->invokeArgs($controller, $dispatch->arguments()) ?: null;
        return [$response, $controller->data()];
    }

    /**
     * Uses the dependency container to create the controller
     *
     * @param ReflectionClass $controllerName
     *
     * @return ControllerInterface
     */
    private function createController(ReflectionClass $controllerName): ControllerInterface
    {
        return $this->container->make($controllerName->getName());
    }
}
