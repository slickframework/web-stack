<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\UserInterface\Console;

use PHPUnit\Framework\Attributes\Test;
use Slick\WebStack\UserInterface\Console\StackDisplayCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class StackDisplayCommandTest extends TestCase
{

    #[Test]
    public function execute(): void
    {
        $command = new StackDisplayCommand();
        $tester = new CommandTester($command);
        $tester->execute([]);

        $this->assertStringContainsString('dispatcher', $tester->getDisplay());
        $this->assertStringContainsString('router', $tester->getDisplay());
        $this->assertStringContainsString('default-response', $tester->getDisplay());
    }
}
