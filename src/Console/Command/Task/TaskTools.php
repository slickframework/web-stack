<?php

/**
 * This file is part of WebStack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Slick\WebStack\Console\Command\Task;


final class TaskTools
{

    public static function getDirName($path, $from = '__DIR__')
    {
        $parts = explode('/', trim($path, '/'));
        array_pop($parts);

        $expression = "dirname({$from})";

        if (count($parts) > 0) {
            return self::getDirName(implode('/', $parts), $expression);
        }

        return $expression;
    }
}
