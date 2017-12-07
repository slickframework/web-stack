<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Exception;

use InvalidArgumentException;
use Slick\WebStack\Exception;

/**
 * Missing Controller Method Exception
 *
 * @package Slick\WebStack\Exception
 */
class MissingControllerMethodException extends InvalidArgumentException implements Exception
{
}
