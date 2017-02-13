<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http\Renderer;

use Aura\Router\Route;
use Slick\Common\Utils\Text;

/**
 * ViewInflector
 *
 * @package Slick\WebStack\Http\Renderer
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
final class ViewInflector implements ViewInflectorInterface
{
    /**
     * @var string
     */
    private $extension;

    /**
     * Creates a view inflector
     *
     * @param string $extension
     */
    public function __construct($extension)
    {
        $this->extension = $extension;
    }

    /**
     * Returns the template name for current request
     *
     * @param Route $route
     *
     * @return string
     */
    public function inflect(Route $route)
    {
        $file  = $route->attributes['controller'].'/';
        $file .= $route->attributes['action'];
        $filtered = Text::camelCaseToSeparator($file, '_');
        $filtered = str_replace('_', '-', strtolower($filtered));
        return "{$filtered}.{$this->extension}";
    }
}