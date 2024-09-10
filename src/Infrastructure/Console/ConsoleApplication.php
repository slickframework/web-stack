<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Console;

use Composer\Autoload\ClassLoader;
use Exception;
use JsonException;
use Slick\Configuration\ConfigurationInterface;
use Slick\ModuleApi\Infrastructure\Console\ConsoleModuleInterface;
use Slick\WebStack\ConsoleModule;
use Slick\WebStack\FrontControllerModule;
use Slick\WebStack\Infrastructure\AbstractApplication;
use Slick\WebStack\Infrastructure\ApplicationSettingsInterface;
use Slick\WebStack\Infrastructure\ComposerParser;
use Symfony\Component\Console\Application;
use function Slick\ModuleApi\constantValue;

/**
 * ConsoleApplication
 *
 * @package Slick\WebStack\Infrastructure\Console
 */
final class ConsoleApplication extends AbstractApplication
{
    
    private ?Application $commandLine = null;
    
    public function __construct(string $rootPath, ?ClassLoader $classLoader = null)
    {
        parent::__construct($rootPath, $classLoader);
        $this->modules[] = new ConsoleModule();
        $this->modules[] = new FrontControllerModule();
    }


    /**
     * @throws JsonException
     * @throws Exception
     */
    public function run(): null
    {
        $container = $this->prepareContainer();
        $commandsDir = $container->get(ConfigurationInterface::class)->get('console.commands_dir');

        $loader = new ConsoleCommandLoader($container, APP_ROOT . $commandsDir);
        $cli = $this->commandLine();

        $cli->setCatchExceptions(true);
        $cli->setCommandLoader($loader);

        foreach ($this->modules as $module) {
            if ($module instanceof ConsoleModuleInterface) {
                $module->configureConsole($cli, $container);
            }
        }

        $cli->run();
        return null;
    }

    /**
     * @throws JsonException
     */
    public function commandLine(): Application
    {
        if (null === $this->commandLine) {
            $this->commandLine = $this->createCommandLine();
        }
        return $this->commandLine;
    }

    public function useCommandLine(?Application $commandLine): void
    {
        $this->commandLine = $commandLine;
    }

    /**
     * @return Application
     * @throws JsonException
     */
    public function createCommandLine(): Application
    {
        $composerParser = new ComposerParser(APP_ROOT . '/composer.json');

        return new Application(
            constantValue('APP_NAME', $composerParser->appName()),
            constantValue('APP_VERSION', $composerParser->version())
        );
    }
}
