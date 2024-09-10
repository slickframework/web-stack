<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator\Factory;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeHandlerInterface;
use Slick\WebStack\Domain\Security\Signature\SignatureHasher;
use Slick\WebStack\Infrastructure\Http\Authenticator\Factory\RememberMeAuthenticatorFactory;
use Slick\WebStack\Infrastructure\Http\Authenticator\RememberMeAuthenticator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;
use Slick\Di\ContainerInterface;
use Slick\Di\Exception\NotFoundException;

class RememberMeAuthenticatorFactoryTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function itsACreatorOfItSelf(): void
    {
        $rmHandler = $this->prophesize(RememberMeHandlerInterface::class)->reveal();
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(RememberMeHandlerInterface::class)->willReturn($rmHandler);
        $container->get(TokenStorageInterface::class)->willReturn($tokenStorage);
        $container->get(LoggerInterface::class)->willReturn($logger);
        $container->register(SignatureHasher::class, Argument::type(SignatureHasher::class))->shouldBeCalled();
        $container->register('remember.me.cookie.options', Argument::type('array'))->shouldBeCalled();
        $authenticator = RememberMeAuthenticatorFactory::create($container->reveal(), ['secret' => 'somSecret', 'cookieName' => 'remember']);
        $this->assertInstanceOf(RememberMeAuthenticator::class, $authenticator);
    }

    #[Test]
    public function failsWhenContainerCannotCreateDependency(): void
    {
        $container = $this->prophesize(ContainerInterface::class);
        $container->get(RememberMeHandlerInterface::class)->willThrow(new NotFoundException('test'));
        $container->register(SignatureHasher::class, Argument::type(SignatureHasher::class))->shouldBeCalled();
        $container->register('remember.me.cookie.options', Argument::type('array'))->shouldBeCalled();
        $this->expectException(LogicException::class);
        $authenticator = RememberMeAuthenticatorFactory::create($container->reveal(), ['secret' => 'somSecret', 'cookieName' => 'remember']);
        $this->assertNull($authenticator);
    }
}
