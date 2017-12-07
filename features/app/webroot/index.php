<?php

/**
 * Application startup script
 */

namespace Test\App;

use Slick\Di\ContainerBuilder;
use Slick\Http\Message\Response;
use Slick\Http\Message\Server\Request;
use Slick\Http\Message\Uri;
use Slick\Http\Server\MiddlewareStack;

require dirname(dirname(dirname(__DIR__))).'/vendor/autoload.php';

/** Application root directory */
define('APP_ROOT', dirname(__DIR__));

$container = (new ContainerBuilder(APP_ROOT.'/config/services'))->getContainer();

/** @var Response $response */
$response = $container->get(MiddlewareStack::class)
    ->process(new Request());


foreach ($response->getHeaders() as $name => $value) {
    $line = implode(', ', $value);
    header("{$name}: $line");
}

echo $response->getBody();