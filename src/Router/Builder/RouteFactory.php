<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Router\Builder;

use Aura\Router\Map;
use Aura\Router\Route;

/**
 * RouteFactory
 *
 * @package Slick\Mvc\Router\Builder
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RouteFactory implements FactoryInterface
{

    /**
     * @var Map
     */
    protected $map;

    /**
     * Receives an array with parameters to create a route or route group
     *
     * @param string       $name The route name
     * @param string|array $data Meta data fo the route
     * @param Map          $map  The route map to populate
     *
     * @return Route
     */
    public function parse($name, $data, Map $map)
    {
        $this->map = $map;
        return $this->simpleRoute($name, $data);
    }

    /**
     * Check if the data is a simple string, create a get with it
     *
     * If not a string pass the data to the construction chain where the route
     * will be set with the data array passed
     *
     * @param string       $name The route name
     * @param string|array $data Meta data fo the route
     *
     * @return Route
     */
    protected function simpleRoute($name, $data)
    {
        if (is_string($data)) {
            return $this->map->get($name, $data);
        }
        return $this->createRoute($name, $data);
    }

    /**
     * Route construct chain start
     *
     * @param string $name The route name
     * @param array  $data Meta data fo the route
     *
     * @return Route
     */
    protected function createRoute($name, array $data)
    {
        $route = $this->map->route($name, $data['path']);
        $allows = array_key_exists('allows', $data)
            ? $data['allows']
            : [];
        $method = ['GET'];
        if (array_key_exists('method', $data)) {
            $method = [$data['method']];
        }
        $route->allows(array_merge($allows, $method));
        return $this->setRouteProperties($route, $data);
    }

    /**
     * Parses data array to set route properties
     *
     * @param Route $route
     * @param array $data
     *
     * @return Route
     */
    protected function setRouteProperties(Route $route, array $data)
    {
        $methods = get_class_methods(Route::class);
        $methods = array_diff($methods, ["allows", "path"]);
        foreach($data as $method => $args) {
            if (in_array($method, $methods)) {
                $route->$method($args);
            }
        }
        return $route;
    }
}