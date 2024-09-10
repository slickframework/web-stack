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
 * InvalidModuleName
 *
 * @package Slick\WebStack\Infrastructure\Exception
 */
final class InvalidModuleName extends RuntimeException implements WebStackException
{

}
