<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Slick\WebStack\Domain\Security\Http\SecurityProfile;

use Slick\WebStack\Domain\Security\Http\AuthenticatorFactoryInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\EntryPointAwareInterface;
use Slick\Di\ContainerInterface;

/**
 * DummyFactory
 *
 * @package Domain\Security\Http\SecurityProfile
 */
final class DummyFactory implements AuthenticatorFactoryInterface
{
    private static AuthenticatorInterface $authenticator;
    public function __construct(AuthenticatorInterface $authenticator)
    {
        self::$authenticator = $authenticator;
    }

    /**
     * @param ContainerInterface $container
     * @param array $properties
     * @param EntryPointAwareInterface|null $factoryHandler
     * @inheritDoc
     */
    public static function create(
        ContainerInterface $container,
        array $properties = [],
        ?EntryPointAwareInterface $factoryHandler = null
    ): AuthenticatorInterface {
        return self::$authenticator;
    }
}
