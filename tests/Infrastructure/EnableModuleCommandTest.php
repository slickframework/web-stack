<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure;

use PHPUnit\Framework\Attributes\Test;
use Slick\WebStack\Infrastructure\EnableModuleCommand;
use PHPUnit\Framework\TestCase;
use Slick\WebStack\Infrastructure\Exception\InvalidModuleName;
use Slick\WebStack\SecurityModule;
use Symfony\Component\Console\Tester\CommandTester;

class EnableModuleCommandTest extends TestCase
{

    #[Test]
    public function initializable(): void
    {
        $command = new EnableModuleCommand(__DIR__);
        $this->assertInstanceOf(EnableModuleCommand::class, $command);
    }

    #[Test]
    public function createFileIfNotExits(): void
    {
        $enabledModulesFile = __DIR__ . '/config/modules/enabled.php';
        $command = new CommandTester(new EnableModuleCommand(__DIR__));
        $command->execute(["module" => 'security']);
        $this->assertTrue(file_exists($enabledModulesFile));
        $this->assertStringContainsString(SecurityModule::class, (string) file_get_contents($enabledModulesFile));
    }

    #[Test]
    public function missingModule(): void
    {
        $enabledModulesFile = __DIR__ . '/config/modules/enabled.php';
        $command = new CommandTester(new EnableModuleCommand(__DIR__));
        $this->expectException(InvalidModuleName::class);
        $command->execute(["module" => 'Some_other_unknown']);
        $this->assertTrue(file_exists($enabledModulesFile));
    }

    protected function tearDown(): void
    {
        if (file_exists(__DIR__ . '/config/modules/enabled.php')) {
            unlink(__DIR__ . '/config/modules/enabled.php');
            rmdir(__DIR__ . '/config/modules');
            rmdir(__DIR__ . '/config');
        }
        parent::tearDown();
    }
}
