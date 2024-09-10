<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader;

use ArrayIterator;
use IteratorAggregate;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\FsWatch\Directory;
use Slick\WebStack\Infrastructure\Console\ConsoleCommandLoader\ConsoleCommandList;
use PHPUnit\Framework\TestCase;
use Slick\WebStack\Infrastructure\Exception\InvalidConsoleLoaderPath;

class ConsoleCommandListTest extends TestCase
{

    use ProphecyTrait;
    private ?Directory $path = null;

    protected function setUp(): void
    {
        $this->path = new Directory(dirname(__DIR__) . '/ConsoleCommandLoader/Loader');
        parent::setUp();
    }

    #[Test]
    public function itsAnIteratorAggregate(): void
    {
        $consoleCommandList = new ConsoleCommandList($this->path);
        $this->assertInstanceOf(ConsoleCommandList::class, $consoleCommandList);
        $this->assertInstanceOf(IteratorAggregate::class, $consoleCommandList);
        $this->assertInstanceOf(ArrayIterator::class, $consoleCommandList->getIterator());
    }

    #[Test]
    public function itsCountable(): void
    {
        $consoleCommandList = new ConsoleCommandList($this->path);
        $this->assertCount(1, $consoleCommandList);
    }

    #[Test]
    public function badPath(): void
    {
        $this->expectException(InvalidConsoleLoaderPath::class);
        $this->assertNull(new ConsoleCommandList('__badPath__'));
    }

    #[Test]
    public function hasCache(): void
    {
        $fileName = sys_get_temp_dir() . '/_slick_console_list' . "_Loader";
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $consoleCommandList = new ConsoleCommandList($this->path);
        $this->assertCount(1, $consoleCommandList);
        $cached = new ConsoleCommandList($this->path);
        $this->assertTrue($cached->fromCache());
    }

    #[Test]
    public function renewCacheOnChange(): void
    {
        $dir = $this->prophesize(Directory::class);
        $dir->hasChanged(Argument::type(Directory\Snapshot::class))->willReturn(true);
        $dir->snapshot()->willReturn($this->path->snapshot());
        $dir->path()->willReturn($this->path->path());
        $consoleCommandList = new ConsoleCommandList($dir->reveal());
        $this->assertFalse($consoleCommandList->fromCache());
    }

    #[Test]
    public function worksLikeAnArray(): void
    {
        $consoleCommandList = new ConsoleCommandList($this->path);
        $class = CommandClass::class;
        $consoleCommandList['other'] = $class;
        $this->assertSame($class, $consoleCommandList['other']);
        unset($consoleCommandList['other']);
        $this->assertFalse($consoleCommandList->offsetExists('other'));
    }
}
