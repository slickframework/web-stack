<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack;

use Slick\WebStack\ConsoleModule;
use PHPUnit\Framework\TestCase;

class ConsoleModuleTest extends TestCase
{

    public function testSettings(): void
    {
        $module = new ConsoleModule();
        $this->assertEquals([
            'console' => [
                'commands_dir' => '/src/UserInterface/Console'
            ]
        ], $module->settings());
    }
}
