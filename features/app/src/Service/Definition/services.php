<?php

/**
 * This file is part of Features\App
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Features\App\Service\Definition;

use Slick\Http\Session;
use Slick\Http\SessionDriverInterface;
use Slick\Template\Template;

// ADD template path
Template::addPath(dirname(dirname(dirname(__DIR__))).'/templates');

$services = [];
$services['routes.file'] = dirname(__DIR__) . '/routes.yml';

$services[SessionDriverInterface::class] =  Session::DRIVER_SERVER;

return $services;