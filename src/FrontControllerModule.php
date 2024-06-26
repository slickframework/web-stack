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
use Slick\Http\Message\Response;
use Slick\WebStack\Infrastructure\ComposerParser;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;
use Slick\WebStack\Infrastructure\FrontController\MiddlewareHandler;
use Slick\WebStack\Infrastructure\FrontController\MiddlewareHandlerInterface;
use Slick\WebStack\Infrastructure\FrontController\MiddlewarePosition;
use Slick\WebStack\Infrastructure\FrontController\Position;
use Slick\WebStack\Infrastructure\FrontController\WebModuleInterface;
use Slick\WebStack\Infrastructure\SlickModuleInterface;

/**
 * FrontControllerModule
 *
 * @package Slick\WebStack
 */
final class FrontControllerModule implements SlickModuleInterface, WebModuleInterface
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

    /**
     * @inheritDoc
     * @return array<string, mixed>
     */
    public function services(): array
    {
        return [
            'default.middleware' => function () {
                return fn() => new Response(
                    200,
                    $this->createDefaultContent(),
                    ['Content-Type' => 'text/html']
                );
            }
        ];
    }


    /**
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        return [];
    }

    /**
     * Returns an array of middleware handlers.
     *
     * @return array<MiddlewareHandlerInterface> The middleware handlers.
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
