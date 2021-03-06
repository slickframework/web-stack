<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Renderer;

use Aura\Router\Route;

/**
 * View Inflector Interface
 *
 * @package Slick\WebStack\Renderer
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
