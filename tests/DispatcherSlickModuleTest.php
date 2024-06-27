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
use Slick\WebStack\DispatcherModule;
use PHPUnit\Framework\TestCase;

class DispatcherSlickModuleTest extends TestCase
{
    use ProphecyTrait;

    private DispatcherModule $module;

    protected function setUp(): void
    {
        $this->module = new DispatcherModule();
    }


    #[Test]
    public function initializable(): void
    {
        $this->assertInstanceOf(DispatcherModule::class, $this->module);
    }

    #[Test]
    public function settings(): void
    {
        $expected = [
            'router' => [
                'cache' => [
                    'enabled' => false,
                    'directory' => sys_get_temp_dir() . '/cache/routes',
                ],
                'resources_path' => APP_ROOT . 'src/UserInterface'
            ]
        ];
        $settings = $this->module->settings($this->prophesize(Dotenv::class)->reveal());
        $this->assertEquals($expected, $settings);
    }
}
