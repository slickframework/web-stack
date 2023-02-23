<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Router\Builder;

use Aura\Router\Exception\ImmutableProperty;
use Aura\Router\Exception\RouteAlreadyExists;
use Aura\Router\Map;
use Aura\Router\Route;

/**
 * RouteFactory
 *
 * @package Slick\WebStack\Router\Builder
 */
class RouteFactory implements FactoryInterface
{

    /**
     * Receives an array with parameters to create a route or route group
     *
     * @param string $name The route name
     * @param array|string $data Meta data fo the route
     * @param Map $map The route map to populate
     *
     * @return Route
     * @throws ImmutableProperty
     * @throws RouteAlreadyExists
     */
    public function parse(string $name, array|string $data, Map $map): Route
    {
        if (is_array($data)) {
            return $this->complexRoute($name, $data, $map);
        }
        return $map->get($name, $data);
    }

    /**
     * Route construct chain start
     *
     * @param string $name The route name
     * @param array $data Meta data fo the route
     * @param Map $map The route map to populate
     *
     * @return Route
     * @throws ImmutableProperty
     * @throws RouteAlreadyExists
     */
    private function complexRoute(string $name, array $data, Map $map): Route
    {
        $route = $map->route($name, $data['path']);
        $method = array_key_exists('method', $data)
            ? [$data['method']]
            : ['GET'];
        $allows = array_key_exists('allows', $data)
            ? $data['allows']
            : [];
        $route->allows(array_merge($method, $allows));
        $this->addProperties($route, $data);
        return $route;
    }

    /**
     * Parses data array to set route properties
     *
     * @param Route $route
     * @param array $data
     *
     * @return void
     */
    private function addProperties(Route $route, array $data): void
    {
        $methods = get_class_methods(Route::class);
        $methods = array_diff($methods, ["allows", "path"]);
        foreach ($data as $method => $args) {
            if (in_array($method, $methods)) {
                $route->$method($args);
            }
        }
    }
}
