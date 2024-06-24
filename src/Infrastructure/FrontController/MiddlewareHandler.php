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
 * MiddlewareHandler
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 */
class MiddlewareHandler implements MiddlewareHandlerInterface
{

    public function __construct(
        protected string $name,
        protected MiddlewarePosition $position,
        protected string|MiddlewareInterface|\Closure $middleware
    ) {
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function middlewarePosition(): MiddlewarePosition
    {
        return $this->position;
    }

    /**
     * @inheritDoc
     */
    public function handler(): string|callable|MiddlewareInterface
    {
        return $this->middleware;
    }
}
