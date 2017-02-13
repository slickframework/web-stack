<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Di\ContainerInterface;
use Slick\Di\Exception\ClassNotFoundException;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\WebStack\Controller\Context;
use Slick\WebStack\Controller\ControllerContextInterface;
use Slick\WebStack\ControllerInterface;
use Slick\WebStack\Exception\ControllerNotFoundException;
use Slick\WebStack\Http\Dispatcher\ControllerDispatch;
use Slick\WebStack\Http\Dispatcher\ControllerDispatchInflectorInterface;
use Slick\WebStack\Http\Dispatcher\ControllerInvokerInterface;

/**
 * Dispatcher Middleware
 *
 * @package Slick\WebStack\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class DispatcherMiddleware extends AbstractMiddleware implements MiddlewareInterface
{
    /**
     * @var ControllerDispatchInflectorInterface
     */
    private $controllerDispatchInflector;

    /**
     * Default context class
     */
    const CONTEXT_CLASS = Context::class;
    /**
     * @var ControllerInvokerInterface
     */
    private $invoker;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ControllerContextInterface
     */
    private $context;

    /**
     * Creates an HTTP request dispatcher middleware
     *
     * @param ControllerDispatchInflectorInterface $controllerDispatchInflector
     * @param ControllerInvokerInterface           $invoker
     * @param ContainerInterface                   $container
     */
    public function __construct(
        ControllerDispatchInflectorInterface $controllerDispatchInflector,
        ControllerInvokerInterface $invoker,
        ContainerInterface $container
    )
    {
        $this->controllerDispatchInflector = $controllerDispatchInflector;
        $this->invoker = $invoker;
        $this->container = $container;
    }

    /**
     * Handles a Request and updated the response
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function handle(
        ServerRequestInterface $request, ResponseInterface $response
    )
    {
        $controllerDispatch = $this->getControllerDispatch($request);
        $controller = $this->createController($controllerDispatch);

        $this->setControllerContext($controller, $request, $response);
        $dataView = $this->invoker->invoke($controller, $controllerDispatch);

        $request = $this->context->getRequest();
        $request = $request->withAttribute('viewData', $dataView);
        $response = $this->context->getResponse();

        return $this->executeNext($request, $response);
    }

    /**
     * Get controller dispatch from provided route
     *
     * @param ServerRequestInterface $request
     *
     * @return Dispatcher\ControllerDispatch
     */
    private function getControllerDispatch(ServerRequestInterface $request)
    {
        $route = $request->getAttribute('route', false);
        $dispatch = $this->controllerDispatchInflector
            ->inflect($route)
        ;
        return $dispatch;
    }

    /**
     * Creates the controller object
     *
     * @param ControllerDispatch $dispatch
     *
     * @return ControllerInterface
     */
    private function createController(ControllerDispatch $dispatch)
    {
        try {
            $controller = $this->container
                ->make($dispatch->getControllerClassName())
            ;
        } catch (ClassNotFoundException $caught) {
            throw new ControllerNotFoundException(
                "The controller '{$dispatch->getControllerClassName()}' was ".
                "not found."
            );
        }

        return $controller;
    }

    /**
     * Sets controller context
     *
     * @param ControllerInterface    $controller
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return Context|ControllerContextInterface
     */
    private function setControllerContext(
        ControllerInterface $controller,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        $contextClass = $this->getContextClass();
        /** @var ControllerContextInterface $context */
        $context = $this->container->make($contextClass);
        $context->register($request, $response);
        $controller->setContext($context);
        $this->context = $context;
        return $context;
    }

    /**
     * Gets the context class from container
     *
     * @return string
     */
    private function getContextClass()
    {
        $class = self::CONTEXT_CLASS;
        if ($this->container->has('controller.context.class')) {
            $class = $this->container->get('controller.context.class');
        }
        return $class;
    }
}