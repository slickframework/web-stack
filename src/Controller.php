<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack;

use Slick\WebStack\Controller\ControllerMethods;

/**
 * Controller
 *
 * @package Slick\WebStack
 */
abstract class Controller implements ControllerInterface
{
    use ControllerMethods;
}
