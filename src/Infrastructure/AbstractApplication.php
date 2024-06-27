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
use Slick\Configuration\ConfigurationInterface;
use Slick\Di\ContainerInterface;
use Slick\WebStack\DispatcherModule;
use Slick\WebStack\FrontControllerModule;
use function Slick\WebStack\importSettingsFile;
use function Slick\WebStack\mergeArrays;

/**
 * AbstractApplication
 *
 * @package Slick\WebStack\Infrastructure
 */
abstract class AbstractApplication
{

    protected DependencyContainerFactory $containerFactory;

    /** @var array<SlickModuleInterface>  */
    protected array $modules;

    protected Dotenv $dotenv;

    /**
     * Creates an AbstractApplication
     *
     * @param string $rootPath
     */
    public function __construct(
        protected readonly string $rootPath
    ) {
        if (!defined('APP_ROOT')) {
            define("APP_ROOT", $this->rootPath);
        }
        $this->dotenv = Dotenv::createImmutable($this->rootPath);
        $this->containerFactory = DependencyContainerFactory::instance();
        $this->modules = [
            new FrontControllerModule(),
            new DispatcherModule(),
        ];
    }

    /**
     * Retrieves the root path of the application.
     *
     * @return string The root path.
     */
    public function rootPath(): string
    {
        return $this->rootPath;
    }

    abstract public function run(): mixed;

    protected function prepareContainer(): ContainerInterface
    {
        $this->loadServices();
        $container = $this->containerFactory->container();
        $container->register('modules', new ArrayConfigurationDriver($this->loadSettings()));
        $container->register(ConfigurationInterface::class, '@modules');
        $container->register(ApplicationSettingsInterface::class, '@modules');
        return $container;
    }


    protected function loadServices(): void
    {
        $services = [];
        foreach ($this->modules as $module) {
            $services = array_merge($services, $module->services());
        }

        $this->containerFactory->loadApplicationServices($this->rootPath(), $services);
    }

    /**
     * Loads the settings from the modules and the external file
     *
     * @return array<string, mixed> The loaded settings
     */
    protected function loadSettings(): array
    {
        $settings = [];
        foreach ($this->modules as $module) {
            $moduleSettings = $module->settings($this->dotenv);
            $settings = mergeArrays($moduleSettings, $settings);
        }
        $importSettingsFile = importSettingsFile(APP_ROOT . '/config/modules.php');
        return mergeArrays($settings, $importSettingsFile);
    }
}
