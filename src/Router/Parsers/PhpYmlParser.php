<?php

/**
 * This file is part of WebStack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Router\Parsers;

use Slick\WebStack\Router\RoutesParser;

/**
 * PhpYmlParser
 *
 * @package Slick\WebStack\Router\Parsers
 */
final class PhpYmlParser implements RoutesParser
{

    public function parse(string $content)
    {
        return yaml_parse($content);
    }

    public function parseFile(string $filename)
    {
        return yaml_parse_file($filename);
    }
}