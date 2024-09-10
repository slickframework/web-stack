<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\UserInterface\Console;

use PHPUnit\Framework\Attributes\Test;
use Slick\WebStack\UserInterface\Console\DescribeModuleCommand;
use PHPUnit\Framework\TestCase;
use Slick\WebStack\UserInterface\Console\DisableModuleCommand;
use Symfony\Component\Console\Tester\CommandTester;

class DescribeModuleCommandTest extends TestCase
{

    #[Test]
    public function initializable(): void
    {
        $command = new DescribeModuleCommand(__DIR__);
        $this->assertInstanceOf(DescribeModuleCommand::class, $command);
    }

    #[Test]
    public function displayModuleInfo(): void
    {
        $command = new DescribeModuleCommand(__DIR__);
        $runner = new CommandTester($command);
        $runner->execute(['module' => 'dispatcher']);
        $this->assertStringContainsString(
            "Core module that integrates routing and dispatching features as middleware ".
            "for a web application.",
            $runner->getDisplay()
        );
    }

    #[Test]
    public function badModuleName(): void
    {
        $command = new CommandTester(new DescribeModuleCommand(__DIR__));
        $expected = "Could not determine module name classname. Check SlickModuleInterface";
        $command->execute(["module" => 'something']);
        $this->assertStringContainsString($expected, $command->getDisplay());
    }
}
