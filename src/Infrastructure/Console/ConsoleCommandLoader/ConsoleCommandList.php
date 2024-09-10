<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader;

use ArrayAccess;
use ArrayIterator;
use Countable;
use Exception;
use IteratorAggregate;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use Slick\FsWatch\Directory;
use Slick\WebStack\Infrastructure\Exception\InvalidConsoleLoaderPath;
use SplFileInfo;

/**
 * ConsoleCommandList
 *
 * @package Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader
 * @implements IteratorAggregate<string, class-string>
 * @implements ArrayAccess<string, class-string>
 */
final class ConsoleCommandList implements IteratorAggregate, ArrayAccess, Countable
{

    /** @var array<string, class-string>  */
    private array $commands = [];

    private const TMP_FILE_NAME = '/_slick_console_list';

    private readonly Directory $directory;

    private bool $fromCache = false;

    /**
     * Creates a ConsoleCommandList
     *
     * @param Directory|string $directory
     */
    public function __construct(Directory|string $directory)
    {
        try {
            $this->directory = is_string($directory) ? new Directory($directory) : $directory;
            if ($this->load()) {
                return;
            }
            $this->loadCommands($this->directory->path());
        } catch (Exception) {
            throw new InvalidConsoleLoaderPath(
                'Provided commands path is not valid or is not found. ' .
                'Could not create command loader. Please check ' . $directory
            );
        }
    }

    /**
     * @inheritDoc
     * @return ArrayIterator<string, class-string>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->commands);
    }

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        return count($this->commands);
    }

    /**
     * ConsoleCommandList fromCache
     *
     * @return bool
     */
    public function fromCache(): bool
    {
        return $this->fromCache;
    }

    private function loadCommands(string $path): void
    {
        $directory = new RecursiveDirectoryIterator($path);
        $iterator = new RecursiveIteratorIterator($directory);
        $phpFiles = new RegexIterator($iterator, '/.*\.php$/i');

        /** @var SplFileInfo $phpFile */
        foreach ($phpFiles as $phpFile) {
            $classFile = new ClassFileDetails($phpFile);
            /** @var class-string $className */
            $className = $classFile->className();
            if ($classFile->isCommand() && $className) {
                $this->commands[$classFile->commandName()] = $className;
            }
        }
        $this->cacheData();
    }

    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->commands[$offset]);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): string
    {
        return $this->commands[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed|class-string $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        /** @var class-string $classString */
        $classString = (string)$value;
        $this->commands[(string) $offset] = $classString;
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        if ($this->offsetExists($offset)) {
            unset($this->commands[$offset]);
        }
    }

    private function cacheData(): void
    {
        $file = $this->cacheFile();
        if (is_file($file)) {
            unlink($file);
        }

        $data = [
            'snapshot' => $this->directory->snapshot(),
            'commands' => $this->commands,
        ];
        file_put_contents($file, serialize($data));
    }

    private function load(): bool
    {
        $file = $this->cacheFile();
        if (!file_exists($file) || !$cacheFile = file_get_contents($file)) {
            return false;
        }

        $cachedData = unserialize($cacheFile);

        /** @var Directory\Snapshot $snapshot */
        $snapshot = $cachedData['snapshot'];
        if ($this->directory->hasChanged($snapshot)) {
            return false;
        }

        $this->commands = $cachedData['commands'];
        $this->fromCache = true;
        return true;
    }

    /**
     * @return string
     */
    private function cacheFile(): string
    {
        $names = explode('/', $this->directory->path());
        return sys_get_temp_dir() . self::TMP_FILE_NAME . "_" . trim(end($names));
    }
}
