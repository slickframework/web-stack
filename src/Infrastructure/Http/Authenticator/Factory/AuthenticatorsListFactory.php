<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator\Factory;

use ArrayIterator;
use Slick\WebStack\Infrastructure\Http\Authenticator\AccessTokenAuthenticator;
use Slick\WebStack\Infrastructure\Http\Authenticator\HttpBasicAuthenticator;
use ArrayAccess;
use IteratorAggregate;
use Slick\Di\ContainerInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\EntryPointAwareInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Traversable;

/**
 * AuthenticatorsListFactory
 *
 * @package Slick\WebStack\Domain\Security\Http\SecurityProfile
 * @template TUser of UserInterface
 * @template T of AuthenticatorInterface
 *
 * @implements ArrayAccess<int|string, T<TUser>>
 * @implements IteratorAggregate<int|string, T<TUser>>
 */
final class AuthenticatorsListFactory implements ArrayAccess, IteratorAggregate, EntryPointAwareInterface
{
    /**
     * @var array<int|string|null, T<TUser>>
     */
    private array $authenticators = [];

    /** @var array<string, mixed|array<string, mixed>>  */
    private static array $presets = [
        'custom' => [
            'className' => null,
            'args' => []
        ],
        'accessToken' => [
            'factoryClass' => AccessTokenAuthenticatorFactory::class,
            'args' => []
        ],
        'httpBasicAuth' => [
            'className' => HttpBasicAuthenticator::class,
            'args' => [
                'realm' => 'Restricted'
            ]
        ],
        'rememberMe' => [
            'factoryClass' => RememberMeAuthenticatorFactory::class,
            'args' => [
                'cookieName' => 'remember_me',
                'secret' => 'some-secured-secret'
            ]
        ],
        'formLogin' => [
            'factoryClass' => FormLoginAuthenticatorFactory::class,
            'args' => []
        ]
    ];

    private ?AuthenticationEntryPointInterface $entryPoint = null;

    /**
     * Creates a AuthenticatorsListFactory
     *
     * @param ContainerInterface $container
     * @param array<string, mixed> $properties
     */
    public function __construct(
        private readonly ContainerInterface $container,
        array $properties
    ) {
        foreach ($properties as $name => $config) {
            if (!array_key_exists($name, self::$presets)) {
                continue;
            }

            $this->createFromFactory($name, $config);
        }
    }

    /**
     * @inheritDoc
     */
    
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->authenticators);
    }

    /**
     * @inheritDoc
     */
    
    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->authenticators);
    }

    /**
     * @inheritDoc
     */
    
    public function offsetGet(mixed $offset): mixed
    {
        return $this->authenticators[$offset] ?? null;
    }

    /**
     * @inheritDoc
     */
    
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->add($offset, $value);
    }

    /**
     * @inheritDoc
     */
    
    public function offsetUnset(mixed $offset): void
    {
        unset($this->authenticators[$offset]);
    }

    /**
     * AuthenticatorsListFactory entryPoint
     *
     * @return AuthenticationEntryPointInterface|null
     */
    public function entryPoint(): ?AuthenticationEntryPointInterface
    {
        return $this->entryPoint;
    }

    /**
     * Adds an authenticator to the collection.
     *
     * @param string|int|null $key The key to associate with the authenticator.
     * @param AuthenticatorInterface<TUser> $authenticator The authenticator to add.
     * @return void
     */
    private function add(string|int|null $key, AuthenticatorInterface $authenticator): void
    {
        $this->authenticators[$key] = $authenticator;
    }

    /**
     * Creates an authenticator instance from the factory.
     *
     * If the factory class for the specified name is not defined, it falls back to creating the authenticator instance
     * from the container.
     *
     * @param int|string $name The name of the authenticator.
     * @param array<string, mixed> $config The configuration options for the authenticator.
     * @return void
     */
    private function createFromFactory(int|string $name, array $config): void
    {
        $customKeys = $name === 'custom' ? array_keys($config): [];
        $keys = array_merge(array_keys(self::$presets[$name]), $customKeys);

        if (!in_array('factoryClass', $keys)) {
            $this->createFromContainer($name, $config);
            return;
        }

        $args = array_merge(self::$presets[$name]['args'] ?? [], $config);
        $className = self::$presets[$name]['factoryClass'] ?? $config['factoryClass'];
        $factoryCallable = [$className, 'create'];

        if (is_callable($factoryCallable)) {
            $authenticator = call_user_func_array($factoryCallable, [$this->container, $args, $this]);

            if ($authenticator instanceof AuthenticationEntryPointInterface) {
                $this->entryPoint = $authenticator;
            }
            $this->authenticators[$name] = $authenticator;
        }
    }

    /**
     * Creates an authenticator instance from the container using the given name and configuration.
     *
     * @param int|string $name The name of the authenticator.
     * @param array<string, mixed> $config The configuration for the authenticator.
     * @return void
     */
    private function createFromContainer(int|string $name, array $config): void
    {
        $args = $this->parseConstructorArgs($name, $config);
        $customClass = isset($config['className']) ? $config['className'] : null;
        $className = $customClass ?? self::$presets[$name]['className'];

        if (isset($config['className'])) {
            unset($args['className']);
        }

        $this->authenticators[$name] = $this->container->make($className, ...array_values($args));
        if ($this->authenticators[$name] instanceof AuthenticationEntryPointInterface) {
            $this->entryPoint = $this->authenticators[$name];
        }
    }

    /**
     * @param int|string $name
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private function parseConstructorArgs(int|string $name, array $config): array
    {
        $args = self::$presets[$name]['args'];
        if ($name === 'custom' && isset($config['args'])) {
            return array_merge($args, $config['args']);
        }

        return array_merge($args, $config);
    }

    /**
     * @inheritDoc
     */
    
    public function withEntryPoint(AuthenticationEntryPointInterface $entryPoint): void
    {
        $this->entryPoint = $entryPoint;
    }
}
