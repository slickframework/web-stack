<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Console;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionException;
use Slick\Di\ContainerInterface;
use Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader\ConsoleCommandList;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
 * ConsoleCommandLoader
 *
 * @package Slick\WebStack\Infrastructure\Console
 */
final class ConsoleCommandLoader implements CommandLoaderInterface
{
    /**
     * @var ConsoleCommandList
     */
    private ConsoleCommandList $commands;

    public function __construct(
        private readonly ContainerInterface $container,
        private readonly string $sourcePath
    ) {
        $this->commands = new ConsoleCommandList($this->sourcePath);
    }

    /**
     * @inheritDoc
     * @throws ReflectionException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $name): Command
    {
        if ($this->has($name)) {
            return $this->createCommand($name);
        }

        throw new CommandNotFoundException("Command '$name' not found.");
    }

    /**
     * @inheritDoc
     */
    public function has(string $name): bool
    {
        return $this->commands->offsetExists($name);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function getNames(): array
    {
        return array_keys($this->commands->getIterator()->getArrayCopy());
    }

    /**
     * Creates a command object based on the provided command name.
     *
     * @param string $name The command name.
     * @return Command The created command object.
     * @throws ReflectionException When reflection is unable to process the command class.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createCommand(string $name): Command
    {
        /** @var class-string<Command> $commandClassName */
        $commandClassName = $this->commands[$name];
        $reflection = new ReflectionClass($commandClassName);
        $constructor = $reflection->getConstructor();
        $arguments = [null];
        $hasNameArgument = false;

        if ($constructor) {
            $ags = $constructor->getParameters();
            $hasNameArgument = isset($ags[0]) && $ags[0]->getName() === 'name';
        }

        return $hasNameArgument
            ? $this->container->make($commandClassName, ...$arguments)
            : $this->container->get($commandClassName);
    }
}
