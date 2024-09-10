<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Routing;

use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Routing\Loader\AttributeClassLoader;
use Symfony\Component\Routing\Route;

/**
 * RoutesAttributeClassLoader
 *
 * @package Slick\WebStack\Infrastructure\Http\Routing
 */
final class RoutesAttributeClassLoader extends AttributeClassLoader
{

    /**
     * @inheritDoc
     * @template T of object
     * @param ReflectionClass<T> $class
     */
    protected function configureRoute(
        Route $route,
        ReflectionClass $class,
        ReflectionMethod $method,
        object $annot
    ): void {
        $route->addDefaults([
            '_controller' => $class->getName(),
            '_action' => $method->getName(),
        ]);
    }
}
