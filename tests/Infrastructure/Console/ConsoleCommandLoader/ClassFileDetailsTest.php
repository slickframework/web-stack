<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader;

use PHPUnit\Framework\Attributes\Test;
use Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader\ClassFileDetails;
use PHPUnit\Framework\TestCase;
use Slick\WebStack\Infrastructure\Exception\InvalidCommandImplementation;
use SplFileInfo;

class ClassFileDetailsTest extends TestCase
{
    private ?ClassFileDetails $classFileDetails = null;

    protected function setUp(): void
    {
        $file = __DIR__ .'/CommandClass.php';
        $this->classFileDetails = new ClassFileDetails(new SplFileInfo($file));
        parent::setUp();
    }

    #[Test]
    public function initializable(): void
    {
        $this->assertInstanceOf(ClassFileDetails::class, $this->classFileDetails);
    }

    #[Test]
    public function checkItsACommand(): void
    {
        $this->assertTrue($this->classFileDetails->isCommand());
    }

    #[Test]
    public function retrieveCommandName(): void
    {
        $this->assertEquals('test', $this->classFileDetails->commandName());
    }

    #[Test]
    public function notAClass()
    {
        $details = new ClassFileDetails(new SplFileInfo(__DIR__ .'/not-a-class.php'));
        $this->assertFalse($details->isCommand());
        $this->expectException(InvalidCommandImplementation::class);
        $details->commandName();
    }

    #[Test]
    public function classWithNoNamespace(): void
    {
        $details = new ClassFileDetails(new SplFileInfo(__DIR__ .'/ClassWithNoNamespace.php'));
        $this->assertEquals('\ClassWithNoNamespace', $details->className());
        $this->expectException(InvalidCommandImplementation::class);
        $details->commandName();
    }

    #[Test]
    public function regularClass(): void
    {
        $details = new ClassFileDetails(new SplFileInfo(__DIR__ .'/RegularClass.php'));
        $this->assertFalse($details->isCommand());
    }
}
