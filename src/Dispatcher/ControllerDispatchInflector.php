<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Dispatcher;

use Aura\Router\Route;

/**
 * ControllerDispatchInflector
 *
 * @package Slick\WebStack\Dispatcher
 */
class ControllerDispatchInflector implements ControllerDispatchInflectorInterface
{
    /**
     * Returns the controller class name from provided route
     *
     * @param Route $route
     *
     * @return ControllerDispatch
     */
    public function inflect(Route $route)
    {
        $arguments = $this->extractAttributes($route);
        return $this->createDispatch($arguments);
    }

    /**
     * Get arguments form route attributes
     *
     * @param Route $route
     * @return array
     */
    private function extractAttributes(Route $route)
    {
        $arguments = [
            'namespace' => null,
            'controller' => null,
            'action' => null,
            'args' => []
        ];
        foreach (array_keys($arguments) as $name) {
            $arguments[$name] = array_key_exists($name, $route->attributes)
                ? $route->attributes[$name]
                : $arguments[$name];
        }
        return $arguments;
    }

    /**
     * Create a controller dispatch with provided arguments
     *
     * @param array $arguments
     *
     * @return ControllerDispatch|Object
     */
    private function createDispatch(array $arguments)
    {
        $arguments['controller'] = $this->filterName($arguments['controller']);
        $data = [
            'className' => ltrim(
                "{$arguments['namespace']}"."\\"."{$arguments['controller']}",
                "\\"
            ),
            'method' => $this->filterName($arguments['action']),
            'arguments' => $arguments['args']
        ];
        $reflection = new \ReflectionClass(ControllerDispatch::class);
        return $reflection->newInstanceArgs($data);
    }

    /**
     * Filters the controller class name
     *
     * @param string $name
     *
     * @return string
     */
    private function filterName($name)
    {
        $name = str_replace(['-', '_'], ' ', $name);
        $words = explode(' ', $name);
        $filtered = '';
        foreach ($words as $word) {
            $filtered .= ucfirst($word);
        }
        return $filtered;
    }
}