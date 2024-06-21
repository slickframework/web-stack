<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\FrontController;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Response;

/**
 * Application
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 */
final readonly class Application
{

    public function __construct(private ServerRequestInterface $request)
    {
    }

    public function run(): ResponseInterface
    {
        return new Response(200, 'It works!', ['Content-Type' => 'text/html']);
    }
}
