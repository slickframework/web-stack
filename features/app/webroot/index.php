<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Slick\Http\Message\Server\Request;
use Slick\WebStack\Infrastructure\FrontController\Application;

define('APP_ROOT', dirname(__DIR__));

require dirname(__DIR__, 3).'/vendor/autoload.php';

$request = new Request();

$application = new Application($request, APP_ROOT);

// ------------------------------------------------------
//  Load any bootstrap actions
// ------------------------------------------------------
$bootstrapFile = APP_ROOT . '/config/bootstrap.php';
if (is_file($bootstrapFile)) {
    require $bootstrapFile;
}


$response = $application->run();

// output the response status
http_response_code($response->getStatusCode());

// Send response headers
foreach ($response->getHeaders() as $name => $value) {
    $line = implode(', ', $value);
    header("$name: $line");
}
// Send response body
echo $response->getBody();
