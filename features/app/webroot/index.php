<?php

/**
 * Features application front controller
 *
 * This file is part of Features\App
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Slick\WebStack\Application;

define('ROOT_PATH', dirname(dirname(dirname(__DIR__))));
require ROOT_PATH . '/vendor/autoload.php';

$application = new Application(
    ROOT_PATH . '/features/app/src/Service/Definition'
);
$response = $application->run();

$response->send();