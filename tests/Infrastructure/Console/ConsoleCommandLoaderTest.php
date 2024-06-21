<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Console;

use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\Di\ContainerInterface;
use Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Test\Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader\Loader\CommandClass;

class ConsoleCommandLoaderTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $commandLoader = new ConsoleCommandLoader($container, __DIR__.'/ConsoleCommandLoader/Loader');
        $this->assertInstanceOf(ConsoleCommandLoader::class, $commandLoader);
    }

    #[Test]
    public function getCommand(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $command = new CommandClass();
        $arguments = [null];
        $container->make(CommandClass::class, ...$arguments)->shouldBeCalled()->willReturn($command);
        $commandLoader = new ConsoleCommandLoader($container->reveal(), __DIR__.'/ConsoleCommandLoader/Loader');
        $this->assertSame($command, $commandLoader->get('test'));
    }

    #[Test]
    public function commandNotFound(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $commandLoader = new ConsoleCommandLoader($container, __DIR__.'/ConsoleCommandLoader/Loader');
        $this->expectException(CommandNotFoundException::class);
        $commandLoader->get('test1');
    }

    #[Test]
    public function getNames(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $commandLoader = new ConsoleCommandLoader($container, __DIR__.'/ConsoleCommandLoader/Loader');
        $this->assertEquals(['test'], $commandLoader->getNames());
    }
}
