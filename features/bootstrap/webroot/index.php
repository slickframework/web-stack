<?php

/**
 * Application startup script
 */

/** @var Composer\Autoload\ClassLoader $autoLoader */
$autoLoader = include dirname(dirname(dirname(__DIR__))).'/vendor/autoload.php';

define('APP_PATH', dirname(__DIR__));

$autoLoader->addPsr4('App\\', APP_PATH.'/App');

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$application = new \Slick\Mvc\Application();
$application->setConfigPath(APP_PATH.'/App/Configuration');

$bsFile = $application->getConfigPath().'/bootstrap.php';
if (is_file($bsFile)) {
    include $bsFile;
}

$response = $application->getResponse();

$response->send();