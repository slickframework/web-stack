<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Routing;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Loader\AttributeDirectoryLoader;
use Symfony\Component\Routing\RouteCollection;

/**
 * MultiPathAttributeDirectoryLoader
 *
 * Aggregates routes from multiple directories. Used when `router.resources_path`
 * is configured as an array in config/modules/dispatcher.php.
 *
 * @package Slick\WebStack\Infrastructure\Http\Routing
 */
final class MultiPathAttributeDirectoryLoader extends Loader
{
    /** @param list<string> $paths Absolute paths to scan for route attributes */
    public function __construct(private readonly array $paths)
    {
        parent::__construct();
    }

    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        $collection = new RouteCollection();
        foreach ($this->paths as $path) {
            if (!is_dir($path)) {
                continue;
            }
            $loader = new AttributeDirectoryLoader(
                new FileLocator($path),
                new RoutesAttributeClassLoader()
            );
            $subCollection = $loader->load($path, $type);
            if ($subCollection !== null) {
                $collection->addCollection($subCollection);
            }
        }
        return $collection;
    }

    public function supports(mixed $resource, ?string $type = null): bool
    {
        return true;
    }
}