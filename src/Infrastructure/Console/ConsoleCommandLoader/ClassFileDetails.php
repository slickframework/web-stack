<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader;

use Slick\WebStack\Infrastructure\Exception\InvalidCommandImplementation;
use SplFileInfo;
use Symfony\Component\Console\Command\Command;

/**
 * ClassFileDetails
 *
 * @package Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader
 */
final class ClassFileDetails
{

    private ?string $namespace = null;

    /** @var class-string|null  */
    private ?string $className = null;

    private ?string $parentClass= null;

    /**
     * Constructs a ClassFileDetails
     *
     * @param SplFileInfo $fileInfo The SplFileInfo object representing the file to be parsed.
     */
    public function __construct(SplFileInfo $fileInfo)
    {
        $contents = file_get_contents($fileInfo->getRealPath());
        if ($contents !== false) {
            $this->parseFile($contents);
        }
    }

    /**
     * Get the FQ class name.
     *
     * @return class-string|null The class name if it exists, otherwise null.
     */
    public function className(): ?string
    {
        return $this->className;
    }

    /**
     * Checks if the class is a Command class.
     *
     * @return bool Returns true if the class is a Command class, false otherwise.
     */
    public function isCommand(): bool
    {
        return $this->parentClass === Command::class;
    }

    /**
     * Returns the command name
     *
     * @return string The command name
     * @throws InvalidCommandImplementation If the class does not extend Command or if the command
     *                                      name cannot be determined.
     */
    public function commandName(): string
    {
        if (!$this->isCommand()) {
            throw new InvalidCommandImplementation(
                "Class does not extend Command. Please extend the command class from ".Command::class
            );
        }

        $callable = [$this->className, 'getDefaultName'];
        $name = !is_callable($callable) ? null : $callable();
        if (!is_string($name)) {
            throw new InvalidCommandImplementation(
                "Could not determine the command name. Did you add the #[AsCommand] attribute?]"
            );
        }
        return $name;
    }

    /**
     * Parses the content of a file to extract class details.
     *
     * @param string $content The content of the file to be parsed.
     *
     * @return void
     */
    private function parseFile(string $content): void
    {
        $regex = '/class (?<name>\w+)?(\sextends\s(?<parent>\w+))?/i';
        $success = preg_match($regex, $content, $matches);
        if ($success === false || !isset($matches['name'])) {
            return;
        }

        $this->parseNamespace($content);
        $name = trim($matches['name']);
        /** @var class-string $fullClassName */
        $fullClassName = "$this->namespace\\$name";
        $this->className = $fullClassName;

        if (!isset($matches['parent'])) {
            return;
        }
        $this->parseParent(trim($matches['parent']), $content);
    }

    /**
     * Parses the namespace from the given content.
     *
     * @param string $content The content to be parsed.
     * @return void
     */
    private function parseNamespace(string $content): void
    {
        $namespaceRegEx = '/namespace(?<namespace>(.*));/i';
        if (!preg_match($namespaceRegEx, $content, $found)) {
            return;
        }

        $this->namespace = trim($found['namespace']);
    }

    /**
     * Parses the parent class in the content and stores it in the property $parentClass.
     *
     * @param string $parent The name of the parent class to search for.
     * @param string $content The content to search for the parent class in.
     * @return void
     */
    private function parseParent(string $parent, string $content): void
    {
        $usesRegex = '/use(?<uses>(.*));/i';
        preg_match_all($usesRegex, $content, $matches);
        $uses = empty($matches['uses']) ? [] : $matches['uses'];

        foreach ($uses as $use) {
            if (str_ends_with(trim($use), "\\$parent")) {
                $this->parentClass = trim($use);
            }
        }
    }
}
