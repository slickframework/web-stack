<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack;

use Composer\Autoload\ClassLoader;
use Dotenv\Dotenv;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slick\Di\ContainerInterface;
use Slick\Di\Definition\ObjectDefinition;
use Slick\ModuleApi\Infrastructure\AbstractModule;
use Slick\ModuleApi\Infrastructure\Console\ConsoleModuleInterface;
use Slick\WebStack\UserInterface\Console\DescribeModuleCommand;
use Slick\WebStack\UserInterface\Console\DisableModuleCommand;
use Slick\WebStack\UserInterface\Console\EnableModuleCommand;
use Slick\WebStack\UserInterface\Console\ListModuleCommand;
use Symfony\Component\Console\Application;
use function Slick\ModuleApi\importSettingsFile;

/**
 * ConsoleModule
 *
 * @package Slick\WebStack
 */
final class ConsoleModule extends AbstractModule implements ConsoleModuleInterface
{

    private const APP_ROOT_KEY = '@app.root';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function configureConsole(Application $cli, ContainerInterface $container): void
    {
        $cli->addCommands([
            $container->get(EnableModuleCommand::class),
            $container->get(DisableModuleCommand::class),
            $container->get(DescribeModuleCommand::class),
            $container->get(ListModuleCommand::class),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function services(): array
    {
        $default = [
            EnableModuleCommand::class => ObjectDefinition
                ::create(EnableModuleCommand::class)
                ->with(self::APP_ROOT_KEY)
            ,
            DisableModuleCommand::class => ObjectDefinition
                ::create(DisableModuleCommand::class)
                ->with(self::APP_ROOT_KEY)
            ,
            DescribeModuleCommand::class => ObjectDefinition
                ::create(DescribeModuleCommand::class)
                ->with(self::APP_ROOT_KEY)
            ,
            ListModuleCommand::class => ObjectDefinition
                ::create(ListModuleCommand::class)
                ->with(self::APP_ROOT_KEY, '@'.ClassLoader::class)
        ];
        return importSettingsFile(dirname(__DIR__).'/config/logging.php', $default);
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

    public function name(): string
    {
        return "console";
    }

    public function description(): string
    {
        return "Provides streamlined command line tools for module management.";
    }
}
