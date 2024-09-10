<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Exception;

use RuntimeException;
use Slick\WebStack\WebStackException;

/**
 * UnresolvedControllerArgument
 *
 * @package Slick\WebStack\Infrastructure\Exception
 */
final class UnresolvedControllerArgument extends RuntimeException implements WebStackException
{

}
