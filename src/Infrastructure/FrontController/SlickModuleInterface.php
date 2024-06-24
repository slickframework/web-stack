<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\FrontController;

use Slick\Di\DefinitionInterface;

/**
 * ModuleInterface
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 */
interface SlickModuleInterface
{

    /**
     * Returns an array of dependency container service definitions.
     *
     * @return array<string, mixed> The array of available services.
     */
    public function services(): array;

    /**
     * Returns an array of module settings.
     *
     * @return array<string, mixed> The array of application settings.
     */
    public function settings(): array;

    /**
     * Returns an array of middleware handlers used in the application.
     *
     * @return array<MiddlewareHandlerInterface> An array of middleware handlers used in the application.
     */
    public function middlewareHandlers(): array;
}
