<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\UserInterface\Console\Security;

use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\Di\ContainerInterface;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use Slick\WebStack\UserInterface\Console\Security\HashPassword;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class HashPasswordTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $asher = new HashPassword($container);
        $this->assertInstanceOf(HashPassword::class, $asher);
    }

    #[Test]
    public function hashAPasswordDefault(): void
    {
        $hasher = $this->prophesize(PasswordHasherInterface::class);
        $plainPassword = 'test';
        $hashedPassword = md5($plainPassword);
        $hasher->hash($plainPassword)->willReturn($hashedPassword);
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(PasswordHasherInterface::class)->willReturn($hasher->reveal());
        $commandTester = new CommandTester(new HashPassword($container->reveal()));
        $commandTester->execute(['plainPassword' => $plainPassword], ['interactive' => false]);
        $this->assertEquals("$hashedPassword\n", $commandTester->getDisplay());
    }

    #[Test]
    public function unknownHasher(): void
    {
        $container = $this->prophesize(ContainerInterface::class)->reveal();
        $commandTester = new CommandTester(new HashPassword($container));
        $this->assertEquals(
            2,
            $commandTester->execute(['plainPassword' => 'test', '-t' => 'test'], ['interactive' => false])
        );
    }

    #[Test]
    public function hasherRetrievalError(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(PasswordHasherInterface::class)->willThrow(new \Exception('Test'));
        $commandTester = new CommandTester(new HashPassword($container->reveal()));
        $this->assertEquals(1, $commandTester->execute(['plainPassword' => 'test'], ['interactive' => false]));
    }
}
