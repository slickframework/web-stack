<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc;

use Aura\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Mvc\Exception\ControllerMethodNotFoundException;
use Slick\Mvc\Exception\ControllerNotFoundException;
use Slick\Mvc\Exception\InvalidControllerException;

/**
 * Dispatcher
 *
 * @package Slick\Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Dispatcher extends AbstractMiddleware implements MiddlewareInterface
{

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $controller;

    /**
     * @var string
     */
    protected $args = [];

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
        /** @var Route $route */
        $route = $request->getAttribute('route', false);
        $this->setAttributes($route);
        $class = $this->namespace.'\\'.$this->controller;
        $controller = $this->createController($class);
        $controller->register($request, $response);

        $this->checkAction($class);

        call_user_func_array([$controller, $this->action], $this->args);
        $request = $controller->getRequest();
        $request = $this->setViewVars($controller, $request);

        return $this->executeNext(
            $request,
            $controller->getResponse()
        );

    }

    /**
     * Sets the data values into request
     * 
     * @param ControllerInterface $controller
     * @param ServerRequestInterface $request
     * 
     * @return ServerRequestInterface|static
     */
    protected function setViewVars(
        ControllerInterface $controller, ServerRequestInterface $request
    ) {
        $key = $controller::REQUEST_ATTR_VIEW_DATA;
        $data = $request->getAttribute($key, []);
        $request = $request->withAttribute(
            $key,
            array_merge($data, $controller->getViewVars())
        );
        return $request;
    }

    /**
     * Creates the controller with provided class name
     *
     * @param string $controller
     *
     * @return ControllerInterface
     */
    protected function createController($controller)
    {
        $this->checkClass($controller);
        $handler = new $controller;
        if (! $handler instanceof ControllerInterface) {
            throw new InvalidControllerException(
                "The class '{$controller}' does not implement ControllerInterface."
            );
        }
        return $handler;
    }

    /**
     * Check if class exists
     *
     * @param string $className
     * @return $this|self|Dispatcher
     */
    protected function checkClass($className)
    {
        if ($className == '\\' || !class_exists($className)) {
            throw new ControllerNotFoundException(
                "The controller '{$className}' was not found."
            );
        }
        return $this;
    }

    /**
     * @param Route $route
     *
     * @return $this|self|Dispatcher
     */
    protected function setAttributes(Route $route)
    {
        $this->namespace = array_key_exists('namespace', $route->attributes)
            ? $route->attributes['namespace']
            : null;
        $this->controller = array_key_exists('controller', $route->attributes)
            ? ucfirst($this->normalize($route->attributes['controller']))
            : null;
        $this->action = array_key_exists('action', $route->attributes)
            ? $this->normalize($route->attributes['action'])
            : null;
        $this->args  = array_key_exists('args', $route->attributes)
            ? $route->attributes['args']
            : [];
        return $this;
    }

    /**
     * Normalize controller/action names
     *
     * @param string $name
     * @return string
     */
    protected function normalize($name)
    {
        $name = str_replace(['_', '-'], '#', $name);
        $words = explode('#', $name);
        array_walk($words, function(&$item){$item = ucfirst($item);});
        return lcfirst(implode('', $words));
    }

    /**
     * Check if action is defined in the controller
     *
     * @param string $className
     *
     * @return Dispatcher
     */
    protected function checkAction($className)
    {
        if (!in_array($this->action, get_class_methods($className))) {
            throw new ControllerMethodNotFoundException(
                "The method {$this->action} is not defined in ".
                "'{$className}' controller."
            );
        }
        return $this;
    }
}