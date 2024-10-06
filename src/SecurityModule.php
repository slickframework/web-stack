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
use Slick\Di\ContainerInterface;
use Slick\ModuleApi\Infrastructure\AbstractModule;
use Slick\ModuleApi\Infrastructure\Console\ConsoleModuleInterface;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandler;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewarePosition;
use Slick\ModuleApi\Infrastructure\FrontController\Position;
use Slick\ModuleApi\Infrastructure\FrontController\WebModuleInterface;
use Slick\WebStack\Infrastructure\Http\AuthorizationMiddleware;
use Slick\WebStack\Infrastructure\Http\SecurityMiddleware;
use Slick\WebStack\UserInterface\Console\Security\GenerateSecretCommand;
use Slick\WebStack\UserInterface\Console\Security\HashPassword;
use Symfony\Component\Console\Application;
use function Slick\ModuleApi\importSettingsFile;

/**
 * SecurityModule
 *
 * @package Slick\WebStack
 */
final class SecurityModule extends AbstractModule implements ConsoleModuleInterface, WebModuleInterface
{
    public function description(): string
    {
        return "Provides authentication and authorization support for web applications.";
    }


    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function configureConsole(Application $cli, ContainerInterface $container): void
    {
        $cli->add($container->get(HashPassword::class));
        $cli->add($container->get(GenerateSecretCommand::class));
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
