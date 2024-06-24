<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator\Factory;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\Http\AuthenticatorFactoryInterface;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeHandlerInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\EntryPointAwareInterface;
use Slick\WebStack\Domain\Security\Signature\SignatureHasher;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\RememberMeAuthenticator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Slick\Di\ContainerInterface;

/**
 * RememberMeAuthenticatorFactory
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator\Factory
 * @template-covariant TUser of UserInterface
 * @implements AuthenticatorFactoryInterface<TUser>
 */
final class RememberMeAuthenticatorFactory implements AuthenticatorFactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param array<string, mixed> $properties
     * @param EntryPointAwareInterface|null $factoryHandler
     * @inheritDoc
     * @phpstan-return RememberMeAuthenticator<UserInterface>
     */
    public static function create(
        ContainerInterface $container,
        array $properties = [],
        ?EntryPointAwareInterface $factoryHandler = null
    ): RememberMeAuthenticator {
        $container->register(SignatureHasher::class, new SignatureHasher(
            $properties['secret'],
            $properties['properties'] ?? [],
        ));
        $container->register('remember.me.cookie.options', $properties);
        try {
            return new RememberMeAuthenticator(
                $container->get(RememberMeHandlerInterface::class),
                $properties['secret'] ?? '',
                $container->get(TokenStorageInterface::class),
                $properties['cookieName'] ?? RememberMeAuthenticator::COOKIE_NAME,
                $container->get(LoggerInterface::class)
            );
        } catch (ContainerExceptionInterface|NotFoundExceptionInterface $e) {
            throw new LogicException($e->getMessage());
        }
    }
}
