<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Router\Builder;

use Aura\Router\Map;
use Aura\Router\Route;

/**
 * Route Factory Interface
 *
 * @package Slick\WebStack\Router\Builder
 */
interface FactoryInterface
{

    /**
     * Receives an array with parameters to create a route or route group
     *
     * @param string $name The route name
     * @param array|string $data Meta data fo the route
     * @param Map          $map  The route map to populate
     *
     * @return Route
     */
    public function parse(string $name, array|string $data, Map $map): Route;
}
