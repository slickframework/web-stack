<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\FrontController;

/**
 * Position
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 */
enum Position: string
{

    case Top = "top";
    case Bottom = "bottom";
    case After = "after";
    case Before = "before";
}
