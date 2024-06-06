<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator\Factory;

use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\EntryPointAwareInterface;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Infrastructure\Http\AuthenticationEntryPoint\FormLoginEntryPoint;
use Slick\WebStack\Infrastructure\Http\Authenticator\Factory\FormLoginAuthenticatorFactory;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler\CsrfTokenHandler;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler\RedirectHandler;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler\RememberMeLoginHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Log\LoggerInterface;
use Slick\Di\ContainerInterface;
use Slick\Di\Exception\NotFoundException;
use Slick\Http\Session\SessionDriverInterface;

class FormLoginAuthenticatorFactoryTest extends TestCase
{

    use ProphecyTrait;

    #[Test]
    public function createAuthenticator(): void
    {
        /** @var array<string, mixed> $properties */
        $properties = Argument::type(FormLoginProperties::class);
        $container = $this->prophesize(ContainerInterface::class);
        $session = $this->prophesize(SessionDriverInterface::class)->reveal();
        $container->get(SessionDriverInterface::class)->willReturn($session);

        $csrfTokenHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $container->make(CsrfTokenHandler::class, $properties)->willReturn($csrfTokenHandler);

        $rememberMeHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $container->make(RememberMeLoginHandler::class, $properties)->willReturn($rememberMeHandler);

        $redirectHandler = $this->prophesize(AuthenticatorHandlerInterface::class)->reveal();
        $container->make(RedirectHandler::class, $session, $properties)->willReturn($redirectHandler);

        $provider = $this->prophesize(UserProviderInterface::class)->reveal();
        $container->get(UserProviderInterface::class)->willReturn($provider);

        $logger = $this->prophesize(LoggerInterface::class)->reveal();
        $container->get(LoggerInterface::class)->willReturn($logger);

        $formEntryPoint = $this->prophesize(AuthenticationEntryPointInterface::class)->reveal();
        $container->make(FormLoginEntryPoint::class, $session, $properties)->willReturn($formEntryPoint);
        $formEntryPointAware = $this->prophesize(EntryPointAwareInterface::class);
        $formEntryPointAware->withEntryPoint($formEntryPoint)->shouldBeCalled();

        $formLoginAuthenticator = FormLoginAuthenticatorFactory::create($container->reveal(), [], $formEntryPointAware->reveal());
        $this->assertInstanceOf(FormLoginAuthenticator::class, $formLoginAuthenticator);
    }

    #[Test]
    public function createAuthenticatorError(): void
    {
        /** @var array<string, mixed> $properties */
        $properties = Argument::type(FormLoginProperties::class);
        $container = $this->prophesize(ContainerInterface::class);
        $session = $this->prophesize(SessionDriverInterface::class)->reveal();
        $container->get(SessionDriverInterface::class)->willReturn($session);

        $container->make(CsrfTokenHandler::class, $properties)->willThrow(new NotFoundException());
        $this->expectException(LogicException::class);
        $formLoginAuthenticator = FormLoginAuthenticatorFactory::create($container->reveal());
        $this->assertNull($formLoginAuthenticator);
    }
}
