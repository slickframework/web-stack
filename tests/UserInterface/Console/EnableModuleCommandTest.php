<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\UserInterface\Console;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Slick\JSONAPI\JsonApiModule;
use Slick\WebStack\Infrastructure\Exception\InvalidModuleName;
use Slick\WebStack\SecurityModule;
use Slick\WebStack\UserInterface\Console\EnableModuleCommand;
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
        $command = new CommandTester(new EnableModuleCommand(__DIR__));
        $expected = "Could not determine module name classname. Check SlickModuleInterface";
        $command->execute(["module" => 'something']);
        $this->assertStringContainsString($expected, $command->getDisplay());
    }

    #[Test]
    public function loadUppercaseNamespace(): void
    {
        $enabledModulesFile = __DIR__ . '/config/modules/enabled.php';
        mkdir(__DIR__ . '/config/modules/', 0755, true);
        $contents = "<?php\n\nreturn[\\".SecurityModule::class."::class];\n";
        file_put_contents($enabledModulesFile, $contents);

        $command = new CommandTester(new EnableModuleCommand(__DIR__));
        $command->execute(["module" => 'json-api']);

        $this->assertTrue(file_exists($enabledModulesFile));
        $this->assertStringContainsString(JsonApiModule::class, (string) file_get_contents($enabledModulesFile));
    }

    #[Test]
    public function existingModule(): void
    {
        $enabledModulesFile = __DIR__ . '/config/modules/enabled.php';
        mkdir(__DIR__ . '/config/modules/', 0755, true);
        $contents = "<?php\n\nreturn[\\".SecurityModule::class."::class];\n";
        file_put_contents($enabledModulesFile, $contents);
        $command = new CommandTester(new EnableModuleCommand(__DIR__));
        $command->execute(["module" => 'Security']);
        $this->assertTrue(file_exists($enabledModulesFile));
        $this->assertStringContainsString(SecurityModule::class, (string) file_get_contents($enabledModulesFile));
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
