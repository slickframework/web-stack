<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure;

use Dotenv\Dotenv;
use Slick\WebStack\Infrastructure\Console\ConsoleModuleInterface;
use Slick\WebStack\Infrastructure\FrontController\WebModuleInterface;
use Symfony\Component\Console\Application;
use function Slick\WebStack\camelToSnake;

/**
 * AbstractModule
 *
 * @package Slick\WebStack\Infrastructure
 */
abstract class AbstractModule implements SlickModuleInterface, ConsoleModuleInterface, WebModuleInterface
{

    /**
     * @inheritDoc
     */
    public function services(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function settings(Dotenv $dotenv): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        $parts = explode('\\', get_called_class());
        $last = array_pop($parts);
        return camelToSnake(str_replace('Module', '', $last));
    }

    /**
     * @inheritDoc
     */
    public function description(): ?string
    {
        return null;
    }

    public function configureConsole(Application $cli): void
    {
        // do nothing: no need to do anything
    }

    public function middlewareHandlers(): array
    {
        return [];
    }
}
