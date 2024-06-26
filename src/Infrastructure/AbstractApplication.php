<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure;

use Slick\WebStack\DispatcherModule;
use Slick\WebStack\FrontControllerModule;
use Slick\WebStack\Infrastructure\Console\ConsoleModuleInterface;
use Slick\WebStack\Infrastructure\FrontController\WebModuleInterface;

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

    protected function loadServices(): void
    {
        $services = [];
        foreach ($this->modules as $module) {
            $services = array_merge($services, $module->services());
        }

        $this->containerFactory->loadApplicationServices($this->rootPath(), $services);
    }
}
