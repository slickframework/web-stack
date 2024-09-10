<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Test\Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

/**
 * CommandClass
 *
 * @package Test\App\Infrastructure\Console\ConsoleCommandLoader
 */
#[AsCommand(name: 'test', description: 'Test command')]
final class CommandClass extends Command
{

}
