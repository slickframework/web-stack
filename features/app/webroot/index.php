<?php

/**
 * Features application front controller
 *
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Slick\WebStack\Application;
use Whoops\Handler\PrettyPageHandler;

define('ROOT_PATH', dirname(dirname(dirname(__DIR__))));
define('APP_PATH', ROOT_PATH . '/features/app');

require ROOT_PATH . '/vendor/autoload.php';

$run     = new Whoops\Run;
$handler = new PrettyPageHandler;
$run->pushHandler($handler);
$run->register();

$application = new Application(
    APP_PATH . '/src/Configuration/Services'
);

$response = $application->run();

$response->send();