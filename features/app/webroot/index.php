<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Slick\Http\Message\Server\Request;
use Slick\WebStack\Infrastructure\FrontController\Application;

require dirname(__DIR__, 3).'/vendor/autoload.php';

// ------------------------------------------------------
//  Initialize application
// ------------------------------------------------------
$request = new Request();
$application = new Application($request, dirname(__DIR__));

// ------------------------------------------------------
//  Load any bootstrap actions
// ------------------------------------------------------
$bootstrapFile = APP_ROOT . '/config/bootstrap.php';
if (is_file($bootstrapFile)) {
    require $bootstrapFile;
}

// ------------------------------------------------------
//  Run application and output the response.
// ------------------------------------------------------
$application->output($application->run());
