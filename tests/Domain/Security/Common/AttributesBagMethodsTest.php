<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Common;

use Slick\WebStack\Domain\Security\Common\AttributesBagMethods;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AttributesBagMethodsTest extends TestCase
{
    use AttributesBagMethods;

    #[Test]
    public function checkAttribute()
    {
        $this->assertSame($this, $this->withAttribute('test', 'ok'));
        $this->assertTrue($this->hasAttribute('test'));
    }

    #[Test]
    public function testWithAttribute()
    {
        $this->assertSame($this, $this->withAttribute('test', 'ok'));
        $this->assertEquals('ok', $this->attribute('test'));
    }

    #[Test]
    public function hasAttributesList()
    {
        $this->assertIsIterable($this->attributes());
    }

    #[Test]
    public function passAttributesList()
    {
        $this->assertSame($this, $this->withAttributes(['foo' => 'bar']));
        $this->assertTrue($this->hasAttribute('foo'));
    }

    #[Test]
    public function setAttribute()
    {
        $this->withAttributes(['foo' => 'bar']);
        $this->assertSame('bar', $this->attribute('foo'));
    }

    #[Test]
    public function setTraversable()
    {
        $this->withAttributes(new \ArrayObject(['foo' => 'bar']));
        $this->assertSame('bar', $this->attribute('foo'));
    }

    #[Test]
    public function requestMissing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->attribute('missing');
    }
}
