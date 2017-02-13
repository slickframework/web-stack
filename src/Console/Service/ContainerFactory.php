<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Service;

use Slick\Di\ContainerBuilder;
use Slick\Di\ContainerInterface;
use Symfony\Component\Console\Command\Command;

/**
 * Container Factory
 *
 * @package Slick\WebStack\Console\Service
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
final class ContainerFactory
{

    /**
     * @var ContainerInterface
     */
    private static $container;

    /**
     * @var Command
     */
    private $command;

    /**
     * Container Factory is only created with self::create() method
     */
    private function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * Creates a new container factory
     *
     * @return ContainerFactory
     */
    public static function create(Command $command)
    {
        $containerFactory = new ContainerFactory($command);
        return $containerFactory;
    }

    /**
     * Get dependency container
     *
     * @return ContainerInterface
     */
    public function container()
    {
        if (!self::$container) {
            $this->createContainer();
        }
        return self::$container;
    }

    /**
     * Creates the dependency container
     */
    private function createContainer()
    {
        $containerBuilder = new ContainerBuilder(__DIR__ . '/Definitions');
        self::$container = $containerBuilder->getContainer();
        self::$container->register(Command::class, $this->command);
    }
}
