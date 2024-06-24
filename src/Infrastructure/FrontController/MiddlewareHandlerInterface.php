<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\FrontController;

use Psr\Http\Server\MiddlewareInterface;

/**
 * MiddlewareHandlerInterface
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 */
interface MiddlewareHandlerInterface
{

    /**
     * Get the name of the object.
     *
     * @return string The name of the object.
     */
    public function name(): string;

    /**
     * Get the position of the middleware.
     *
     * @return MiddlewarePosition The position of the middleware.
     */
    public function middlewarePosition(): MiddlewarePosition;

    /**
     * Get the handler for the middleware.
     *
     * @return string|callable|MiddlewareInterface The handler for the middleware.
     */
    public function handler(): string|callable|MiddlewareInterface;
}
