<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Features\App\Config\Services;

use Slick\Http\Message\Response;

$services = [
    'default.middleware.test' => function () {
        return fn() => new Response(
            200,
            json_encode(['foo' => 'bar']),
            ['Content-Type' => 'application/json']
        );
    }
];

return $services;
