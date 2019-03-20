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
 * View Inflector
 *
 * @package Slick\WebStack\Renderer
 */
class ViewInflector implements ViewInflectorInterface
{
    /**
     * @var string
     */
    private $extension;

    private static $hasPcreUnicodeSupport;

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
        $filtered = self::camelCaseToSeparator($file, '_');
        $filtered = str_replace('_', '-', strtolower($filtered));
        return "{$filtered}.{$this->extension}";
    }

    /**
     * Converts camel case strings to words separated by provided string
     *
     * @param string $text The text to evaluate
     * @param string $sep  The separator (or glue) for the words
     *
     * @return string
     */
    public static function camelCaseToSeparator($text, $sep = " ")
    {
        if (!is_scalar($text) && !is_array($text)) {
            return $text;
        }
        if (self::hasPcreUnicodeSupport()) {
            $pattern = array(
                '#(?<=(?:\p{Lu}))(\p{Lu}\p{Ll})#',
                '#(?<=(?:\p{Ll}|\p{Nd}))(\p{Lu})#'
            );
            $replacement = array($sep.'\1', $sep.'\1');
        } else {
            $pattern = array(
                '#(?<=(?:[A-Z]))([A-Z]+)([A-Z][a-z])#',
                '#(?<=(?:[a-z0-9]))([A-Z])#');
            $replacement = array('\1'.$sep.'\2', $sep.'\1');
        }
        return preg_replace($pattern, $replacement, $text);
    }

    /**
     * Is PCRE compiled with Unicode support?
     *
     * @return bool
     */
    public static function hasPcreUnicodeSupport()
    {
        if (self::$hasPcreUnicodeSupport === null) {
            self::$hasPcreUnicodeSupport =
                defined('PREG_BAD_UTF8_OFFSET_ERROR') &&
                @preg_match('/\pL/u', 'a') == 1;
        }
        return self::$hasPcreUnicodeSupport;
    }
}
