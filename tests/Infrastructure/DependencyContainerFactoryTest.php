<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure;

use PHPUnit\Framework\Attributes\Test;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\Di\ContainerBuilderInterface;
use Slick\Di\ContainerInterface;
use Slick\Di\DefinitionLoaderInterface;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;
use PHPUnit\Framework\TestCase;

class DependencyContainerFactoryTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function singleton(): void
    {
        $factory = DependencyContainerFactory::instance();
        $this->assertInstanceOf(DependencyContainerFactory::class, $factory);
    }

    #[Test]
    public function hasAContainer(): void
    {
        $factory = DependencyContainerFactory::instance();
        $this->assertInstanceOf(ContainerInterface::class, $factory->container());
    }

    #[Test]
    public function canUseAnExistingContainerBuilder(): void
    {
        $builder = $this->prophesize(ContainerBuilderInterface::class);
        $factory = DependencyContainerFactory::instance();
        $factory->container();
        $builder->setContainer(Argument::type(ContainerInterface::class))->shouldBeCalled()->willReturn($builder->reveal());
        $this->assertSame($factory, $factory->withBuilder($builder->reveal()));
    }

    #[Test]
    public function loadContainerDefinitions(): void
    {
        $builder = $this->prophesize(ContainerBuilderInterface::class);
        $loader = $this->prophesize(DefinitionLoaderInterface::class);
        $definitionLoader = $loader->reveal();

        $builder->load($loader)->shouldBeCalled()->willReturn($builder);
        $factory = DependencyContainerFactory::instance();
        $this->assertSame($factory, $factory->withBuilder($builder->reveal()));
        $factory->load($definitionLoader);
    }

    protected function tearDown(): void
    {
        $reflection = new \ReflectionClass(DependencyContainerFactory::instance());
        $reflection->setStaticPropertyValue('instance', null);
    }
}
