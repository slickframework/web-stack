<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack;

use JsonException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slick\Di\ContainerInterface;
use Slick\Http\Message\Response;
use Slick\ModuleApi\Infrastructure\AbstractModule;
use Slick\ModuleApi\Infrastructure\Console\ConsoleModuleInterface;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandler;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandlerInterface;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewarePosition;
use Slick\ModuleApi\Infrastructure\FrontController\Position;
use Slick\ModuleApi\Infrastructure\FrontController\WebModuleInterface;
use Slick\ModuleApi\Infrastructure\SlickModuleInterface;
use Slick\WebStack\Infrastructure\ComposerParser;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;
use Slick\WebStack\UserInterface\Console\StackDisplayCommand;
use Symfony\Component\Console\Application;
use function Slick\ModuleApi\importSettingsFile;

/**
 * FrontControllerModule
 *
 * @package Slick\WebStack
 */
final class FrontControllerModule extends AbstractModule implements
    SlickModuleInterface,
    WebModuleInterface,
    ConsoleModuleInterface
{

    private ComposerParser $composerParser;

    /**
     * Creates a FrontControllerSlickModule
     *
     * @throws JsonException
     */
    public function __construct()
    {
        $this->composerParser = new ComposerParser(APP_ROOT.'/composer.json');
    }

    public function description(): ?string
    {
        return "Core module that initializes a web application using the front controller pattern.";
    }

    /**
     * @inheritDoc
     * @return array<string, mixed>
     */
    public function services(): array
    {
        $default = [
            'default.middleware' => function () {
                return fn() => new Response(
                    200,
                    $this->createDefaultContent(),
                    ['Content-Type' => 'text/html']
                );
            }
        ];
        return importSettingsFile(dirname(__DIR__).'/config/logging.php', $default);
    }

    /**
     * Returns an array of middleware handlers.
     *
     * @return array<MiddlewareHandlerInterface> The middleware handlers.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function middlewareHandlers(): array
    {
        return [
            new MiddlewareHandler(
                'default-response',
                new MiddlewarePosition(Position::Bottom),
                DependencyContainerFactory::instance()->container()->get('default.middleware')
            )
        ];
    }

    public function configureConsole(Application $cli, ContainerInterface $container): void
    {
        $args = [null];
        $cli->add($container->make(StackDisplayCommand::class, ...$args));
    }


    /**
     * Creates the default content for the HTML body.
     *
     * @return string The default content for the HTML body.
     */
    private function createDefaultContent(): string
    {
        $head = "<html lang=\"en\"><title>%s</title><body>%s</body></html>";
        $body = sprintf(
            "<h1>%s <span class=\"small\">%s</span></h1><p>%s</p>",
            $this->composerParser->appName(),
            $this->composerParser->version(),
            $this->composerParser->description()
        );
        return sprintf($head, $this->composerParser->appName(), $body);
    }
}
