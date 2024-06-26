<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Console;

use Slick\WebStack\Infrastructure\SlickModuleInterface;
use Symfony\Component\Console\Application;

/**
 * ConsoleModuleInterface
 *
 * @package Slick\WebStack\Infrastructure\Console
 */
interface ConsoleModuleInterface extends SlickModuleInterface
{

    public function configureConsole(Application $cli): void;
}
