<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Exception;

use Slick\WebStack\Domain\Security\SecurityException;
use InvalidArgumentException;

/**
 * MissingAttributeException
 *
 * @package Slick\WebStack\Domain\Security\Exception
 */
final class MissingAttributeException extends InvalidArgumentException implements SecurityException
{

}