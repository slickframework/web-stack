<?php

/**
 * This file is part of sata/orm package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Slick\Di\Definition\ObjectDefinition;
use Slick\Configuration\Configuration;
use Slick\Http\PhpEnvironment\Request;

/**
 * Default DI services definitions
 */
$services = [];

$services['request'] = ObjectDefinition::create(
    \Slick\Http\PhpEnvironment\Request::class
);

return $services;