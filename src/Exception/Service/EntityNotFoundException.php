<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Exception\Service;

use RuntimeException;
use Slick\Mvc\Service\ServiceException;

/**
 * Entity Not Found Exception
 * 
 * @package Slick\Mvc\Exception\Service
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityNotFoundException extends RuntimeException implements
    ServiceException
{

}