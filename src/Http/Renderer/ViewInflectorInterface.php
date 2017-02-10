<?php

/**
 * This file is part of slick/mvc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http\Renderer;

use Aura\Router\Route;

/**
 * View Inflector Interface
 *
 * @package Slick\WebStack\Http\Renderer
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
interface ViewInflectorInterface
{

    /**
     * Returns the template name for current request
     *
     * @param Route $route
     *
     * @return string
     */
    public function inflect(Route $route);
}
