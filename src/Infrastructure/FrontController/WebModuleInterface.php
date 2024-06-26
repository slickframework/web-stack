<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\FrontController;

use Slick\WebStack\Infrastructure\SlickModuleInterface;

/**
 * WebModuleInterface
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 */
interface WebModuleInterface extends SlickModuleInterface
{

    /**
     * Returns an array of middleware handlers used in the application.
     *
     * @return array<MiddlewareHandlerInterface> An array of middleware handlers used in the application.
     */
    public function middlewareHandlers(): array;
}
