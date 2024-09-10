<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http;

use Slick\WebStack\Domain\Security\Http\SecurityProfile\EntryPointAwareInterface;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\Di\ContainerInterface;

/**
 * AuthenticatorFactoryInterface
 *
 * @package Slick\WebStack\Domain\Security\Http
 * @template-covariant TUser of UserInterface
 */
interface AuthenticatorFactoryInterface
{

    /**
     * Create an Authenticator.
     *
     * @param ContainerInterface $container The container interface for retrieving dependencies.
     * @param array<string, mixed> $properties [optional] An array of properties used for authentication.
     * @param EntryPointAwareInterface|null $factoryHandler
     *
     * @return AuthenticatorInterface<TUser> The created instance of the AuthenticatorInterface,
     * or null if the creation failed.
     * @throws SecurityException When any error occurs trying to create the authenticator
     */
    public static function create(
        ContainerInterface $container,
        array $properties = [],
        ?EntryPointAwareInterface $factoryHandler = null
    ): AuthenticatorInterface;
}
