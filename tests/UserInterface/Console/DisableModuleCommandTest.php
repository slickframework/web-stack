<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\UserInterface\Console;

use PHPUnit\Framework\Attributes\Test;
use Slick\WebStack\DispatcherModule;
use PHPUnit\Framework\TestCase;
use Slick\WebStack\SecurityModule;
use Slick\WebStack\UserInterface\Console\DisableModuleCommand;
use Symfony\Component\Console\Tester\CommandTester;

class DisableModuleCommandTest extends TestCase
{

    #[Test]
    public function initializable(): void
    {
        $command = new DisableModuleCommand(__DIR__);
        $this->assertInstanceOf(DisableModuleCommand::class, $command);
    }

    #[Test]
    public function doNothingWhenFileDoesNotExist(): void
    {
        $command = new CommandTester(new DisableModuleCommand(__DIR__));
        $command->execute(["module" => 'something']);
        $this->assertStringContainsString(
            "Module 'something' is not installed. No change was made.",
            $command->getDisplay()
        );
    }

    #[Test]
    public function updateDocument(): void
    {
        $command = new CommandTester(new DisableModuleCommand(__DIR__));
        mkdir(__DIR__ . '/config/modules', 0755, true);
        $contents = "<?php\n\nreturn[\\".SecurityModule::class."::class, \\".DispatcherModule::class."::class];\n";
        $enabledModulesFile = __DIR__ . '/config/modules/enabled.php';
        file_put_contents($enabledModulesFile, $contents);
        $command->execute(["module" => 'security']);
        $content = file_get_contents($enabledModulesFile);
        $this->assertStringNotContainsString(SecurityModule::class, $content);
    }

    #[Test]
    public function emptyDocument(): void
    {
        $command = new CommandTester(new DisableModuleCommand(__DIR__));
        mkdir(__DIR__ . '/config/modules', 0755, true);
        $contents = "<?php\n\nreturn[\\".SecurityModule::class."::class];\n";
        $enabledModulesFile = __DIR__ . '/config/modules/enabled.php';
        file_put_contents($enabledModulesFile, $contents);
        $command->execute(["module" => 'security']);
        $content = file_get_contents($enabledModulesFile);
        $this->assertStringNotContainsString(SecurityModule::class, $content);
    }

    #[Test]
    public function moduleDoesNotExist(): void
    {
        $command = new CommandTester(new DisableModuleCommand(__DIR__));
        mkdir(__DIR__ . '/config/modules', 0755, true);
        $contents = "<?php\n\nreturn[\\".DispatcherModule::class."::class];\n";
        $enabledModulesFile = __DIR__ . '/config/modules/enabled.php';
        file_put_contents($enabledModulesFile, $contents);
        $command->execute(["module" => 'security']);
        $content = file_get_contents($enabledModulesFile);
        $this->assertStringNotContainsString(SecurityModule::class, $content);
    }

    #[Test]
    public function bandName(): void
    {
        $command = new CommandTester(new DisableModuleCommand(__DIR__));
        mkdir(__DIR__ . '/config/modules', 0755, true);
        $contents = "<?php\n\nreturn[\\".DispatcherModule::class."::class];\n";
        $enabledModulesFile = __DIR__ . '/config/modules/enabled.php';
        file_put_contents($enabledModulesFile, $contents);
        $command->execute(["module" => 'securities']);
        $content = file_get_contents($enabledModulesFile);
        $this->assertStringNotContainsString(SecurityModule::class, $content);
    }

    protected function tearDown(): void
    {
        if (file_exists(__DIR__ . '/config/modules/enabled.php')) {
            unlink(__DIR__ . '/config/modules/enabled.php');
            rmdir(__DIR__ . '/config/modules');
            rmdir(__DIR__ . '/config');
        }
    }


}
