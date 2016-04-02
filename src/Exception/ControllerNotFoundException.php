<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Exception;

use RuntimeException;
use Slick\Mvc\Exception;

/**
 * Controller Not Found Exception
 *
 * @package Slick\Mvc\Exception
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class ControllerNotFoundException extends RuntimeException implements Exception
{

}