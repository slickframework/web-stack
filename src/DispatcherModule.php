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
use Slick\ModuleApi\Infrastructure\AbstractModule;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandler;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewarePosition;
use Slick\ModuleApi\Infrastructure\FrontController\Position;
use Slick\ModuleApi\Infrastructure\FrontController\WebModuleInterface;
use Slick\ModuleApi\Infrastructure\SlickModuleInterface;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;
use Slick\WebStack\Infrastructure\Http\DispatcherMiddleware;
use Slick\WebStack\Infrastructure\Http\RoutingMiddleware;
use function Slick\ModuleApi\importSettingsFile;

/**
 * DispatcherSlickModule
 *
 * @package Slick\WebStack
 */
final class DispatcherModule extends AbstractModule implements SlickModuleInterface, WebModuleInterface
{
    public function description(): ?string
    {
        return "Core module that integrates routing and dispatching features as middleware for a web application.";
    }


    /**
     * @inheritDoc
     */
    public function services(): array
    {
        return require dirname(__DIR__) . '/config/routing.php';
    }

    /**
     * @inheritDoc
     */
    public function settings(Dotenv $dotenv): array
    {

        $settingsFile = APP_ROOT .'/config/modules/dispatcher.php';
        $defaultSettings = [
            'router' => [
                'cache' => [
                    'enabled' => true,
                    'directory' => sys_get_temp_dir() . '/cache/routes',
                ],
                'resources_path' => APP_ROOT . '/src/UserInterface'
            ]
        ];
        return importSettingsFile($settingsFile, $defaultSettings);
    }

    /**
     * @inheritDoc
     */
    public function middlewareHandlers(): array
    {
        return [
            new MiddlewareHandler('router', new MiddlewarePosition(Position::Top), RoutingMiddleware::class),
            new MiddlewareHandler(
                'dispatcher',
                new MiddlewarePosition(Position::Before, 'default-response'),
                new DispatcherMiddleware(DependencyContainerFactory::instance()->container())
            ),
        ];
    }
}
