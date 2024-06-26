<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack;

use Slick\WebStack\Infrastructure\Console\ConsoleModuleInterface;
use Slick\WebStack\Infrastructure\EnableModuleCommand;
use Symfony\Component\Console\Application;

/**
 * ConsoleModule
 *
 * @package Slick\WebStack
 */
final class ConsoleModule implements Infrastructure\Console\ConsoleModuleInterface
{

    public function configureConsole(Application $cli): void
    {
        $cli->add(new EnableModuleCommand());
    }

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
    public function settings(): array
    {
        $custom = [];
        $settingsFile = APP_ROOT .'/config/settings/console.php';
        if (file_exists($settingsFile)) {
            $custom = require $settingsFile;
        }

        $defaultSettings = [
            'console' => ['commands_dir' => '/src/UserInterface'],
        ];
        return mergeArrays($defaultSettings, $custom);
    }
}
