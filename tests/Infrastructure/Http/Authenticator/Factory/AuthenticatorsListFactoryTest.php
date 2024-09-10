<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator\Factory;

use IteratorAggregate;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\Di\ContainerInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\Factory\AuthenticatorsListFactory;
use PHPUnit\Framework\TestCase;
use Test\Slick\WebStack\Domain\Security\Http\SecurityProfile\DummyFactory;

class AuthenticatorsListFactoryTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $props = [];
        $factory = new AuthenticatorsListFactory($container, $props);
        $this->assertInstanceOf(AuthenticatorsListFactory::class, $factory);
    }

    #[Test]
    public function unknownAuthenticator(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $props = [
            'tes' => []
        ];
        $factory = new AuthenticatorsListFactory($container, $props);
        $this->assertInstanceOf(AuthenticatorsListFactory::class, $factory);
        $this->assertCount(0, $factory);
    }

    #[Test]
    public function behavesLikeAnArray(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $props = [];
        $factory = new AuthenticatorsListFactory($container, $props);
        $authenticator = $this->prophesize(AuthenticatorInterface::class)->reveal();
        $this->assertInstanceOf(\ArrayAccess::class, $factory);
        $factory['authenticator'] = $authenticator;
        $this->assertSame($authenticator, $factory['authenticator']);
        $this->assertTrue(isset($factory['authenticator']));
        unset($factory['authenticator']);
        $this->assertFalse(isset($factory['authenticator']));
    }

    #[Test]
    public function canBeIterated(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $props = [];
        $factory = new AuthenticatorsListFactory($container, $props);
        $authenticator = $this->prophesize(AuthenticatorInterface::class)->reveal();
        $factory['authenticator'] = $authenticator;

        $this->assertInstanceOf(IteratorAggregate::class, $factory);
        $this->assertInstanceOf(\ArrayIterator::class, $factory->getIterator());
        $this->assertSame($authenticator, $factory->getIterator()['authenticator']);
    }

    #[Test]
    public function createFromFactory(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $authenticator = $this->prophesize(AuthenticatorInterface::class);
        $authenticator->willImplement(AuthenticationEntryPointInterface::class);
        $factoryClass = new DummyFactory($authenticator->reveal());
        $props = [
            'custom' => ['factoryClass' => $factoryClass::class]
        ];
        $factory = new AuthenticatorsListFactory($container, $props);
        $this->assertInstanceOf(AuthenticatorInterface::class, $factory['custom']);
    }

    #[Test]
    public function isAnEntryPointAware(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $props = [];
        $factory = new AuthenticatorsListFactory($container, $props);
        $entryPoint = $this->prophesize(AuthenticationEntryPointInterface::class)->reveal();
        $factory->withEntryPoint($entryPoint);
        $this->assertSame($entryPoint, $factory->entryPoint());
    }
}
