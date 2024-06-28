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
use function Slick\WebStack\importSettingsFile;
use function Slick\WebStack\mergeArrays;

/**
 * AbstractApplication
 *
 * @package Slick\WebStack\Infrastructure
 */
abstract class AbstractApplication
{

    private const MODULES_PATH = "/config/modules/enabled.php";

    protected DependencyContainerFactory $containerFactory;

    /** @var array<SlickModuleInterface>  */
    protected array $modules = [];

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

    /**
     * Run the application and return the result.
     *
     * @return mixed The result of running the code.
     */
    abstract public function run(): mixed;

    /**
     * Prepare the container by loading modules, settings, and services.
     *
     * @return ContainerInterface The prepared container.
     */
    protected function prepareContainer(): ContainerInterface
    {
        $this->loadModules();
        $settingsDriver = new ArrayConfigurationDriver($this->loadSettings());
        $this->loadServices();
        $container = $this->containerFactory->container();
        $container->register('settings', $settingsDriver);
        $container->register(ConfigurationInterface::class, '@settings');
        $container->register(ApplicationSettingsInterface::class, '@settings');
        $container->register('app.root', $this->rootPath());
        return $container;
    }

    /**
     * Load services from all modules and register them in the container.
     *
     * @return void
     */
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
        $this->dotenv->safeLoad();
        foreach ($this->modules as $module) {
            $moduleSettings = $module->settings($this->dotenv);
            $settings = mergeArrays($moduleSettings, $settings);
        }
        $importSettingsFile = importSettingsFile(APP_ROOT . '/config/modules.php');
        return mergeArrays($settings, $importSettingsFile);
    }

    /**
     * Load modules into the application.
     */
    private function loadModules(): void
    {
        $file = $this->rootPath . self::MODULES_PATH;
        if (!is_file($file)) {
            return;
        }

        $modules = require $file;
        foreach ($modules as $module) {
            $loadedModule = new $module();
            if ($loadedModule instanceof SlickModuleInterface) {
                $this->modules[] = $loadedModule;
            }
        }
    }
}
