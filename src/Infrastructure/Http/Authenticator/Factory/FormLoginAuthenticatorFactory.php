<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator\Factory;

use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\Http\AuthenticatorFactoryInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\EntryPointAwareInterface;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\WebStack\Infrastructure\Http\AuthenticationEntryPoint\FormLoginEntryPoint;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Slick\Di\ContainerInterface;
use Slick\Http\Session\SessionDriverInterface;

/**
 * FormLoginAuthenticatorFactory
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator\Factory
 * @template-covariant TUser of UserInterface
 * @implements AuthenticatorFactoryInterface<TUser>
 */
final class FormLoginAuthenticatorFactory implements AuthenticatorFactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param array<string, mixed> $properties
     * @param EntryPointAwareInterface|null $factoryHandler
     * @inheritDoc
     *
     * @phpstan-return FormLoginAuthenticator<UserInterface>
     */
    public static function create(
        ContainerInterface $container,
        array $properties = [],
        ?EntryPointAwareInterface $factoryHandler = null
    ): FormLoginAuthenticator {
        $properties = new FormLoginProperties($properties);
        try {
            $csrfHandler = $container->make(LoginFormAuthenticatorHandler\CsrfTokenHandler::class, $properties);
            $rememberMeHAndler = $container->make(
                LoginFormAuthenticatorHandler\RememberMeLoginHandler::class,
                $properties
            );
            $session = $container->get(SessionDriverInterface::class);
            $redirectHandler = $container->make(
                LoginFormAuthenticatorHandler\RedirectHandler::class,
                $session,
                $properties
            );
            $factoryHandler?->withEntryPoint(
                $container->make(
                    FormLoginEntryPoint::class,
                    $session,
                    $properties
                )
            );
            $handlers = [$csrfHandler, $rememberMeHAndler, $redirectHandler];
            return new FormLoginAuthenticator(
                provider: $container->get(UserProviderInterface::class),
                handler: new FormLoginAuthenticator\FormLoginHandler($handlers),
                session: $session,
                properties: $properties,
                logger: $container->get(LoggerInterface::class)
            );
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
            throw new LogicException($e->getMessage());
        }
    }
}
