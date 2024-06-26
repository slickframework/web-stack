<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

/**
 * EnableModuleCommand
 *
 * @package Slick\WebStack\Infrastructure
 */
#[AsCommand(
    name: "modules:enable",
    description: "Enable Slick modules.",
    aliases: ["enable"]
)]
final class EnableModuleCommand extends Command
{

}
