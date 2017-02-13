<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http\Dispatcher;

use Aura\Router\Route;

/**
 * Controller Class Inflector Interface
 *
 * @package Slick\WebStack\Http\Dispatcher
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface ControllerDispatchInflectorInterface
{

    /**
     * Returns the controller class name from provided route
     *
     * @param Route $route
     *
     * @return ControllerDispatch
     */
    public function inflect(Route $route);
}