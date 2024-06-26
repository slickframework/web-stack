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
use JsonException;
use Slick\WebStack\ConsoleModule;
use Slick\WebStack\Infrastructure\AbstractApplication;
use Slick\WebStack\Infrastructure\ComposerParser;
use Symfony\Component\Console\Application;
use function Slick\WebStack\constantValue;

/**
 * ConsoleApplication
 *
 * @package Slick\WebStack\Infrastructure\Console
 */
final class ConsoleApplication extends AbstractApplication
{
    
    private ?Application $commandLine = null;
    
    public function __construct(string $rootPath)
    {
        parent::__construct($rootPath);
        $this->modules[] = new ConsoleModule();
    }


    /**
     * @throws JsonException
     * @throws Exception
     */
    public function run(): null
    {
        $this->loadServices();
        $container = $this->containerFactory->container();

        $loader = new ConsoleCommandLoader($container, APP_ROOT . '/src/UserInterface/Console');
        $cli = $this->commandLine();

        $cli->setCatchExceptions(true);
        $cli->setCommandLoader($loader);

        foreach ($this->modules as $module) {
            if ($module instanceof ConsoleModuleInterface) {
                $module->configureConsole($cli);
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
