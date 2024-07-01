<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure;

use Dotenv\Dotenv;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\WebStack\Infrastructure\AbstractModule;
use Symfony\Component\Console\Application;

class AbstractModuleTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable(): void
    {
        $module = new DummyModule();
        $this->assertInstanceOf(AbstractModule::class, $module);
    }

    #[Test]
    public function services(): void
    {
        $module = new DummyModule();
        $this->assertEquals([], $module->services());
    }

    #[Test]
    public function settings(): void
    {
        $dotenv = $this->prophesize(Dotenv::class)->reveal();
        $module = new DummyModule();
        $this->assertEquals([], $module->settings($dotenv));
    }

    #[Test]
    public function computeName(): void
    {
        $module = new DummyModule();
        $this->assertEquals('dummy', $module->name());
        $this->assertEquals(null, $module->description());
    }

    #[Test]
    public function middlewares(): void
    {
        $module = new DummyModule();
        $module->configureConsole($this->prophesize(Application::class)->reveal());
        $this->assertEquals([], $module->middlewareHandlers());
    }
}
