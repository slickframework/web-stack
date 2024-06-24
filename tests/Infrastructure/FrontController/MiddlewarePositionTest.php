<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\FrontController;

use PHPUnit\Framework\Attributes\Test;
use Slick\WebStack\Infrastructure\Exception\InvalidMiddlewarePosition;
use Slick\WebStack\Infrastructure\FrontController\MiddlewarePosition;
use PHPUnit\Framework\TestCase;
use Slick\WebStack\Infrastructure\FrontController\Position;

class MiddlewarePositionTest extends TestCase
{
    private $position;
    private $reference;

    private $pos;

    protected function setUp(): void
    {
        parent::setUp();
        $this->position = Position::Before;
        $this->reference = 'dispatcher';
        $this->pos = new MiddlewarePosition($this->position, $this->reference);
    }


    public function testReference()
    {
        $this->assertSame($this->reference, $this->pos->reference());
    }

    #[Test]
    public function initializable(): void
    {
        $this->assertInstanceOf(MiddlewarePosition::class, $this->pos);
        $this->expectException(InvalidMiddlewarePosition::class);
        $pos = new MiddlewarePosition(Position::After);
        $this->assertNull($pos);
    }

    #[Test]
    public function position(): void
    {
        $this->assertSame($this->position, $this->pos->position());
    }
}
