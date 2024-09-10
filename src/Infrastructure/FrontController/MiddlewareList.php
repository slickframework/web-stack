<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\FrontController;

use Doctrine\Common\Collections\ArrayCollection;
use IteratorAggregate;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandlerInterface;
use Slick\ModuleApi\Infrastructure\FrontController\Position;

/**
 * MiddlewareList
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 * @implements IteratorAggregate<string, MiddlewareHandlerInterface>
 */
final class MiddlewareList implements IteratorAggregate
{

    /** @var ArrayCollection<string, MiddlewareHandlerInterface>  */
    private ArrayCollection $middlewares;


    public function __construct()
    {
        $this->middlewares = new ArrayCollection();
    }

    /**
     * @inheritDoc
     * @return ArrayCollection<string, MiddlewareHandlerInterface>
     */
    public function getIterator(): ArrayCollection
    {
        return $this->middlewares;
    }

    public function add(MiddlewareHandlerInterface $middleware): self
    {
        $newList = [];

        $position = $middleware->middlewarePosition();
        if ($position->position() === Position::Top) {
            $newList[$middleware->name()] = $middleware;
        }

        foreach ($this->middlewares as $key => $current) {
            if ($position->reference() === $key) {
                if ($position->position() === Position::Before) {
                    $newList[$middleware->name()] = $middleware;
                    $newList[$key] = $current;
                    continue;
                }

                if ($position->position() === Position::After) {
                    $newList[$key] = $current;
                    $newList[$middleware->name()] = $middleware;
                    continue;
                }
            }

            $newList[$key] = $current;
        }

        if ($position->position() === Position::Bottom) {
            $newList[$middleware->name()] = $middleware;
        }

        $this->middlewares = new ArrayCollection($newList);
        return $this;
    }
}
