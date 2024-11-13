<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator\Factory;

use Slick\Di\ContainerInterface;
use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Slick\WebStack\Domain\Security\Http\AccessToken\Extractor\HeaderAccessTokenExtractor;
use Slick\WebStack\Domain\Security\Http\AuthenticatorFactoryInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\EntryPointAwareInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\AccessTokenAuthenticator;

/**
 * AccessTokenAuthenticatorFactory
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator\Factory
 * @implements AuthenticatorFactoryInterface<UserInterface>
 */
final class AccessTokenAuthenticatorFactory implements AuthenticatorFactoryInterface
{

    /**
     * @var array<string, string>
     */
    private static array $defaultProperties = [
        "extractor" => HeaderAccessTokenExtractor::class,
        "handler" => AccessTokenHandlerInterface::class
    ];

    /**
     * @inheritDoc
     */
    public static function create(
        ContainerInterface $container,
        array $properties = [],
        ?EntryPointAwareInterface $factoryHandler = null
    ): AccessTokenAuthenticator {
        $properties = array_merge(self::$defaultProperties, $properties);
        if (!$container->has($properties["handler"])) {
            throw new LogicException(
                "Access token handler missing: define a class that implements the " .
                AccessTokenHandlerInterface::class ." and set it as the 'handler' parameter in the ".
                "'accessToken' authenticator within the 'settings.php' file."
            );
        }

        return new AccessTokenAuthenticator(
            $container->make($properties["extractor"]),
            $container->get($properties["handler"])
        );
    }
}
