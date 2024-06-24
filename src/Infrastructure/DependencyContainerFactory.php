<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure;

use Slick\Di\ContainerBuilder;
use Slick\Di\ContainerBuilderInterface;
use Slick\Di\ContainerInterface;
use Slick\Di\DefinitionLoader\AutowireDefinitionLoader;
use Slick\Di\DefinitionLoader\DirectoryDefinitionLoader;
use Slick\Di\DefinitionLoader\FileDefinitionLoader;
use Slick\Di\DefinitionLoaderInterface;
use Slick\Di\Exception;

/**
 * DependencyContainerFactory
 *
 * @package Slick\WebStack\Infrastructure
 */
final class DependencyContainerFactory
{
    private static ?DependencyContainerFactory $instance = null;

    private ?ContainerInterface $container = null;

    private ContainerBuilderInterface $builder;
    /**
     *
     */
    private function __construct()
    {
        $this->builder = new ContainerBuilder();
    }

    /**
     * ContainerFactory instance
     *
     * @return DependencyContainerFactory
     */
    public static function instance(): DependencyContainerFactory
    {

        if (null === self::$instance) {
            self::$instance = new DependencyContainerFactory();
        }
        return self::$instance;
    }

    /**
     * ContainerFactory container
     *
     * @return ContainerInterface
     */
    public function container(): ContainerInterface
    {
        if (null === $this->container) {
            $this->container = $this->builder->getContainer();
            $this->container->register(ContainerInterface::class, $this->container);
        }
        return $this->container;
    }

    public function withBuilder(ContainerBuilderInterface $builder): DependencyContainerFactory
    {
        if (null !== $this->container) {
            $builder->setContainer($this->container);
            $this->container = null;
        }

        $this->builder = $builder;
        return $this;
    }

    /**
     * @throws Exception
     */
    public function load(DefinitionLoaderInterface $definitionLoader): DependencyContainerFactory
    {
        $this->builder->load($definitionLoader);
        return $this;
    }

    /**
     * Loads application services from given source path and services file.
     *
     * @param string $sourcePath Path to the directory containing service definitions
     * @param array<string, mixed> $services Array of service definitions file paths
     * @return DependencyContainerFactory
     */
    public function loadApplicationServices(string $sourcePath, array $services): DependencyContainerFactory
    {
        $this
            ->load(new DirectoryDefinitionLoader($sourcePath . '/config/services'))
            ->load(new AutowireDefinitionLoader($sourcePath . '/src'))
            ->load(new FileDefinitionLoader($services));
        return $this;
    }
}
