<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\PhpPasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slick\Http\Session\SessionDriverInterface;

/**
 * ProfileFactoryTrait
 *
 * @package Slick\WebStack\Domain\Security\Http
 * @template-covariant TUser of UserInterface
 */
trait ProfileFactoryTrait
{

    /** @var array<string, mixed>  */
    protected static array $defaultOptions = [
        "secured" => true,
        "passwordHasher" => PhpPasswordHasher::class,
        "authenticators" => [],
        "stateless" => true,
        "tokenStorage" => TokenStorageInterface::class,
        "sessionDriver" => SessionDriverInterface::class,
        "entryPoint" => null
    ];

    /**
     * Create a session driver based on a given profile modules.
     *
     * @param array<string, mixed> $profile The profile configuration.
     * @return SessionDriverInterface The created session driver.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createSessionDriver(array $profile): SessionDriverInterface
    {
        $config = array_merge(self::$defaultOptions, $profile);
        return $this->container->get($config['sessionDriver']);
    }

    /**
     * Create a token storage based on a given profile.
     *
     * @param array<string, mixed> $profile The profile configuration.
     * @return TokenStorageInterface<UserInterface> The created token storage.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createTokenStorage(array $profile): TokenStorageInterface
    {
        $config = array_merge(self::$defaultOptions, $profile);
        return $this->container->get($config['tokenStorage']);
    }

    /**
     * Creates a password hasher.
     *
     * @param array<string, mixed> $profile The profile array containing the configuration options.
     * @return PasswordHasherInterface The created password hasher.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createPasswordHasher(array $profile): PasswordHasherInterface
    {
        $config = array_merge(self::$defaultOptions, $profile);
        return $this->container->get($config['passwordHasher']);
    }

    /**
     * Creates a user provider instance based on a given provider class.
     *
     * @param string $providerClass The class name of the user provider
     *
     * @return UserProviderInterface<UserInterface> The user provider instance
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createUserProvider(string $providerClass): UserProviderInterface
    {
        return $this->container->get($providerClass);
    }
}
