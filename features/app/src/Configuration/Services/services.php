<?php

/**
 * This file is part of slick/mvc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Features\App\Configuration\Services;

use Slick\Template\Template;

// ADD template path
Template::addPath(dirname(dirname(dirname(__DIR__))).'/templates');

$services = [];
$services['routes.file'] = dirname(__DIR__) . '/routes.yml';

return $services;