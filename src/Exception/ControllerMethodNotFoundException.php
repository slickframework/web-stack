<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Exception;

use LogicException;
use Slick\Mvc\Exception;

/**
 * Controller Method Not Found Exception
 *
 * @package Slick\Mvc\Exception
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ControllerMethodNotFoundException extends LogicException implements
    Exception
{

}