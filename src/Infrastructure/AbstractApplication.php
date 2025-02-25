<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure;

use Composer\Autoload\ClassLoader;
use Dotenv\Dotenv;
use Slick\Configuration\ConfigurationInterface;
use Slick\Configuration\Driver\Environment;
use Slick\Configuration\PriorityConfigurationChain;
use Slick\Di\ContainerInterface;
use Slick\ModuleApi\Infrastructure\SlickModuleInterface;
use function Slick\ModuleApi\importSettingsFile;
use function Slick\ModuleApi\mergeArrays;

/**
 * AbstractApplication
 *
 * @package Slick\WebStack\Infrastructure
 */
abstract class AbstractApplication
{

    public const MODULES_PATH = "/config/modules/enabled.php";

    protected DependencyContainerFactory $containerFactory;

    /** @var array<SlickModuleInterface>  */
    protected array $modules = [];

    protected Dotenv $dotenv;

    private ?ContainerInterface $container = null;

    /**
     * Creates an AbstractApplication
     *
     * @param string $rootPath
     * @param ClassLoader|null $classLoader
     */
    public function __construct(
        protected readonly string $rootPath,
        protected readonly ?ClassLoader $classLoader = null
    ) {
        if (!defined('APP_ROOT')) {
            define("APP_ROOT", $this->rootPath);
        }
        $this->dotenv = Dotenv::createImmutable($this->rootPath);
        $this->containerFactory = DependencyContainerFactory::instance();
    }

    public function container(): ContainerInterface
    {
        return $this->prepareContainer();
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
        if ($this->container) {
            return $this->container;
        }

        $this->loadModules();
        $settingsDriver = new PriorityConfigurationChain();
        $settingsDriver->add(new Environment(), 10);
        $settingsDriver->add(new ArrayConfigurationDriver($this->loadSettings()), 30);
        $this->loadServices();
        $container = $this->containerFactory->container();
        $container->register('settings', $settingsDriver);
        $container->register(ConfigurationInterface::class, '@settings');
        $container->register('app.root', $this->rootPath());

        if ($this->classLoader) {
            $container->register(ClassLoader::class, $this->classLoader);
        }

        $this->container = $container;
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
    public function loadModules(): void
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
