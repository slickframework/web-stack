<?php

/**
 * Application HTTP middleware stack services
 */

namespace Features\App\Config\Services;

use Slick\Http\Session;

$services = [];

$services[Session\SessionDriverInterface::class] = '@session.driver';
$services['session.driver'] = function () {
    return Session::create(Session::DRIVER_SERVER);
};

return $services;