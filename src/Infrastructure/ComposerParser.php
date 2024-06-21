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

/**
 * ComposerParser
 *
 * @package Slick\WebStack\Infrastructure
 */
final class ComposerParser
{
    /**
     * @var object{
     *     'name': string,
     *     'version': string|int
     * }
     */
    private object $data;

    /**
     * @throws JsonException
     */
    public function __construct(string $composerFile)
    {
        if (!is_file($composerFile) || !$composerContents = file_get_contents($composerFile)) {
            throw new RuntimeException("Composer file '$composerFile' does not exist or is not readable.");
        }
        $this->data = json_decode(json: $composerContents, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Returns the inflated name of the application.
     *
     * @return string The inflated name of the application.
     */
    public function appName(): string
    {
        return $this->inflateName($this->data->name);
    }

    /**
     * Returns the version.
     *
     * @return string The version.
     */
    public function version(): string
    {
        return (string) $this->data->version;
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
}
