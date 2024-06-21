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
use Slick\WebStack\Infrastructure\Console\DirectoryCommandLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Test\Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader\Loader\CommandClass;

class DirectoryCommandLoaderTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function isInitializable(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $this->assertInstanceOf(DirectoryCommandLoader::class, new DirectoryCommandLoader($container));
    }

    #[Test]
    public function addLoader(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $command = new CommandClass();
        $options = [null];
        $container->make(CommandClass::class, ...$options)->willReturn($command);
        $loader = new DirectoryCommandLoader($container->reveal());
        $loader->add(__DIR__.'/ConsoleCommandLoader/Loader');
        $this->assertTrue($loader->has('test'));
        $this->assertSame($command, $loader->get('test'));
        $this->assertEquals(['test'], $loader->getNames());
    }

    #[Test]
    public function unknownCommand(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $command = new CommandClass();
        $options = [null];
        $container->make(CommandClass::class, ...$options)->willReturn($command);
        $loader = new DirectoryCommandLoader($container->reveal());
        $this->assertFalse($loader->has('unknownCommand'));
        $this->expectException(CommandNotFoundException::class);
        $loader->get('unknownCommand');
    }
}
