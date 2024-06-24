<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\FrontController;

use Slick\WebStack\Infrastructure\Exception\InvalidMiddlewarePosition;

/**
 * MiddlewarePosition
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 */
final readonly class MiddlewarePosition
{
    /**
     * Create a middleware position
     *
     * @param Position $position The position of the middleware.
     * @param string|null $reference The name of the reference middleware.
     *
     * @throws InvalidMiddlewarePosition When a reference middleware is required but not provided.
     */
    public function __construct(
        private Position $position,
        private ?string  $reference = null
    ) {
        $needsReference = $this->position === Position::After || $this->position === Position::Before;
        if ($needsReference && !$this->reference) {
            throw new InvalidMiddlewarePosition(
                "When defining the position of a middleware to be before or after ".
                "another middleware, you need to specify the reference middleware name."
            );
        }
    }

    /**
     * Get the position of the middleware
     *
     * @return Position The position of the middleware.
     */
    public function position(): Position
    {
        return $this->position;
    }

    /**
     * Get the name of the reference middleware.
     *
     * @return string|null The name of the reference middleware, or null if not set.
     */
    public function reference(): ?string
    {
        return $this->reference;
    }
}
