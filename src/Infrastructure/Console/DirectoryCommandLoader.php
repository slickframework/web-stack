<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Console;

use Slick\Di\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * DirectoryCommandLoader
 *
 * @package Slick\WebStack\Infrastructure\Console
 */
final class DirectoryCommandLoader implements CommandLoaderInterface
{
    /** @var array<ConsoleCommandLoader>  */
    private array $loaders = [];

    public function __construct(
        private readonly ContainerInterface $container
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): Command
    {
        foreach ($this->loaders as $loader) {
            if ($loader->has($name)) {
                return $loader->get($name);
            }
        }

        throw new CommandNotFoundException("Command '$name' not found.");
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        foreach ($this->loaders as $loader) {
            if ($loader->has($name)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function getNames(): array
    {
        $names = [];
        foreach ($this->loaders as $loader) {
            $names = array_merge($names, $loader->getNames());
        }
        return $names;
    }

    public function add(string $sourcePath): void
    {
        $this->loaders[] = new ConsoleCommandLoader($this->container, $sourcePath);
    }
}
