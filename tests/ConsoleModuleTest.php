<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack;

use Dotenv\Dotenv;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\WebStack\ConsoleModule;
use PHPUnit\Framework\TestCase;

class ConsoleModuleTest extends TestCase
{
    use ProphecyTrait;

    public function testSettings(): void
    {
        $dotenv = $this->prophesize(Dotenv::class);
        $module = new ConsoleModule();
        $this->assertEquals([
            'console' => [
                'commands_dir' => '/src/UserInterface/Console'
            ]
        ], $module->settings($dotenv->reveal()));
    }
}
