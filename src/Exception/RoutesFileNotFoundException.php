<?php

/**
 * This file is part of slick/mvc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Exception;

use InvalidArgumentException;
use Slick\WebStack\Exception;

/**
 * Routes File Not Found Exception
 *
 * @package Slick\WebStack\Exception
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class RoutesFileNotFoundException extends InvalidArgumentException implements
    Exception
{

}
