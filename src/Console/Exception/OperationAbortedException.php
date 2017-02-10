<?php

/**
 * This file is part of slick/mvc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Exception;

use RuntimeException;
use Slick\WebStack\Console\ConsoleException;

/**
 * Operation Aborted Exception
 *
 * @package Slick\WebStack\Console\Exception
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class OperationAbortedException extends RuntimeException implements
    ConsoleException
{

}
