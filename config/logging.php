<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Config;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Slick\Di\Definition\ObjectDefinition;

$services = [];

$services['stream.logger.handler'] = ObjectDefinition
    ::create(StreamHandler::class)
    ->with(APP_ROOT."/logs/debug.log", Level::Debug)
;

$services[LoggerInterface::class] = '@default.logger';
$services['default.logger'] = ObjectDefinition
    ::create(Logger::class)
    ->with($_ENV['LOGGER_NAME'] ?? 'app_logger')
    ->call('pushHandler')->with('@stream.logger.handler')
;

return $services;
