<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Exception;

use InvalidArgumentException;
use Slick\WebStack\Exception;

/**
 * Controller Not Found Exception
 *
 * @package Slick\WebStack\Exception
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ControllerNotFoundException extends InvalidArgumentException implements
    Exception
{

}
