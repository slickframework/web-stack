<?php

/**
 * This file is part of WebStack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Router;

/**
 * RoutesFileParser
 *
 * @package Slick\WebStack\Router
 */
interface RoutesParser
{

    /**
     * Parses a routes definition string into a PHP value/array
     *
     * @param string $content
     * @return mixed
     */
    public function parse(string $content);

    /**
     * Parses a routes definition file into a PHP value/array
     *
     * @param string $filename
     * @return mixed
     */
    public function parseFile(string $filename);
}
