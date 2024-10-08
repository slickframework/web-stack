<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack;

use Dotenv\Dotenv;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Slick\WebStack\FrontControllerModule;
use PHPUnit\Framework\TestCase;

class FrontControllerSlickModuleTest extends TestCase
{
    use ProphecyTrait;

    private FrontControllerModule $module;

    protected function setUp(): void
    {
        $this->module = new FrontControllerModule();
    }

    #[Test]
    public function initializable(): void
    {
        $this->assertInstanceOf(FrontControllerModule::class, $this->module);
    }

    #[Test]
    public function serviceDefinesADefaultResponse(): void
    {
        $services = $this->module->services();
        $this->assertArrayHasKey('default.middleware', $services);
        $this->assertInstanceOf(ResponseInterface::class, $services['default.middleware']()());
    }

    #[Test]
    public function emptySettings(): void
    {
        $dotenv = $this->prophesize(Dotenv::class);
        $this->assertEmpty($this->module->settings($dotenv->reveal()));
    }
}
