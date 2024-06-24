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

    public function settings(): array;

    public function middlewareHandlers(): array;
}
