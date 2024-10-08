<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure;

use JsonException;
use RuntimeException;
use Slick\WebStack\Infrastructure\Exception\InvalidComposerFile;

/**
 * ComposerParser
 *
 * @package Slick\WebStack\Infrastructure
 */
final class ComposerParser
{
    /**
     * @var array<string, mixed>
     */
    private array $data;

    /**
     * @throws JsonException
     */
    public function __construct(string $composerFile)
    {
        if (!is_file($composerFile) || !$composerContents = file_get_contents($composerFile)) {
            throw new InvalidComposerFile("Composer file '$composerFile' does not exist or is not readable.");
        }
        $this->data = json_decode(json: $composerContents, associative: true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Returns the inflated name of the application.
     *
     * @return string The inflated name of the application.
     */
    public function appName(): string
    {
        return $this->inflateName($this->data["name"]);
    }

    /**
     * Returns the version.
     *
     * @return string The version.
     */
    public function version(): string
    {
        return (string) $this->data["version"];
    }

    /**
     * Inflates the name.
     *
     * @param string $name The name to inflate.
     *
     * @return string The inflated name.
     */
    private function inflateName(string $name): string
    {
        $parts = explode('/', str_replace(['-', '_', '.'], ' ', $name));
        $owner = ucwords(trim($parts[0]));
        $app = ucfirst(trim($parts[1]));
        return "$owner's $app";
    }

    /**
     * Returns the description of the application.
     *
     * @return string The description of the application.
     */
    public function description(): string
    {
        return (string) $this->data["description"];
    }

    /**
     * Returns the autoload paths for the given key.
     *
     * @param string $key The autoload path key. Default is "psr-4".
     * @return array<string, string> The autoload paths for the given key.
     */
    public function autoload(string $key = "psr-4"): array
    {
        return (array) $this->data["autoload"][$key];
    }

    /**
     * Returns the PSR-4 namespaces declared in the autoload configuration.
     *
     * @return array<string> An array of PSR-4 namespaces.
     */
    public function psr4Namespaces(): array
    {
        return array_keys($this->autoload());
    }
}
