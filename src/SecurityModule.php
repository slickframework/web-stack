<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack;

use Dotenv\Dotenv;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slick\WebStack\Infrastructure\Console\ConsoleModuleInterface;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;
use Slick\WebStack\Infrastructure\FrontController\MiddlewareHandler;
use Slick\WebStack\Infrastructure\FrontController\MiddlewarePosition;
use Slick\WebStack\Infrastructure\FrontController\Position;
use Slick\WebStack\Infrastructure\FrontController\WebModuleInterface;
use Slick\WebStack\Infrastructure\Http\AuthorizationMiddleware;
use Slick\WebStack\Infrastructure\Http\SecurityMiddleware;
use Slick\WebStack\UserInterface\Console\Security\HashPassword;
use Symfony\Component\Console\Application;

/**
 * SecurityModule
 *
 * @package Slick\WebStack
 */
final class SecurityModule implements ConsoleModuleInterface, WebModuleInterface
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function configureConsole(Application $cli): void
    {
        $cli->add(DependencyContainerFactory::instance()->container()->get(HashPassword::class));
    }

    /**
     * @inheritDoc
     */
    public function services(): array
    {
        return importSettingsFile(dirname(__DIR__) . '/config/security.php');
    }

    /**
     * @inheritDoc
     */
    public function settings(Dotenv $dotenv): array
    {
        $dotenv->required('APP_SECRET');
        return [];
    }

    public function middlewareHandlers(): array
    {
        return [
            new MiddlewareHandler(
                'security',
                new MiddlewarePosition(Position::Before, 'router'),
                SecurityMiddleware::class
            ),
            new MiddlewareHandler(
                'authorization',
                new MiddlewarePosition(Position::Before, 'dispatcher'),
                AuthorizationMiddleware::class
            ),
        ];
    }
}
