<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\UserInterface\Console;

use PHPUnit\Framework\Attributes\Test;
use Slick\WebStack\UserInterface\Console\ListModuleCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ListModuleCommandTest extends TestCase
{

    #[Test]
    public function initializable(): void
    {
        $loader = require dirname(__DIR__, 3) . '/vendor/autoload.php';
        $command = new ListModuleCommand(__DIR__, $loader);
        $this->assertInstanceOf(ListModuleCommand::class, $command);
    }

    #[Test]
    public function testExecute(): void
    {
        $loader = require dirname(__DIR__, 3) . '/vendor/autoload.php';
        $command = new ListModuleCommand(__DIR__, $loader);
        $tester = new CommandTester($command);

        $tester->execute([]);

        $this->assertStringContainsString('dispatcher', $tester->getDisplay());
        $this->assertStringContainsString('front_controller', $tester->getDisplay());
        $this->assertStringContainsString('security', $tester->getDisplay());
        $this->assertStringContainsString('console', $tester->getDisplay());
    }
}
