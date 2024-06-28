<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack;

use Dotenv\Dotenv;
use Slick\Di\Definition\ObjectDefinition;
use Slick\WebStack\Infrastructure\Console\ConsoleModuleInterface;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;
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
        $container = DependencyContainerFactory::instance()->container();
        $cli->addCommands([
            $container->get(EnableModuleCommand::class)
        ]);
    }

    /**
     * @inheritDoc
     */
    public function services(): array
    {
        return [
            EnableModuleCommand::class => ObjectDefinition
                ::create(EnableModuleCommand::class)
                ->with('@app.root')
            ,
        ];
    }

    /**
     * @inheritDoc
     */
    public function settings(Dotenv $dotenv): array
    {
        $settingsFile = APP_ROOT .'/config/modules/console.php';
        $defaultSettings = [
            'console' => ['commands_dir' => '/src/UserInterface'],
        ];
        return importSettingsFile($settingsFile, $defaultSettings);
    }
}
