<?php

/**
 * Features application front controller
 *
 * This file is part of Slick\WebStack\
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Slick\WebStack\Application;

define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/vendor/autoload.php';

$application = new Application(
    ROOT_PATH . '/src/Infrastructure/Web/UI/Service/Definitions'
);
$response = $application->run();

$response->send();