<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure;

use Slick\WebStack\Infrastructure\ArrayConfigurationDriver;
use PHPUnit\Framework\TestCase;

class ArrayConfigurationDriverTest extends TestCase
{

    public function testConstruct(): void
    {
        $driver = new ArrayConfigurationDriver(['foo' => ['bar' => 'baz']]);
        $this->assertInstanceOf(ArrayConfigurationDriver::class, $driver);
        $this->assertEquals('baz', $driver->get('foo.bar'));
    }
}
