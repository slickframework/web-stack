<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\FrontController;

use PHPUnit\Framework\Attributes\Test;
use Slick\WebStack\Infrastructure\FrontController\MiddlewareHandler;
use PHPUnit\Framework\TestCase;
use Slick\WebStack\Infrastructure\FrontController\MiddlewarePosition;
use Slick\WebStack\Infrastructure\FrontController\Position;

class MiddlewareHandlerTest extends TestCase
{
    private MiddlewareHandler $handler;
    private $name;
    private $position;
    private $middleware;

    protected function setUp(): void
    {
        $this->name = "Handler";
        $this->position = new MiddlewarePosition(Position::Top);
        $this->middleware = 'some.service';
        $this->handler = new MiddlewareHandler(
            $this->name,
            $this->position,
            $this->middleware
        );
    }


    public function testName()
    {
        $this->assertEquals($this->name, $this->handler->name());
    }

    #[Test]
    public function initializable(): void
    {
        $this->assertInstanceOf(MiddlewareHandler::class, $this->handler);
    }

    public function testMiddlewarePosition()
    {
        $this->assertSame($this->position, $this->handler->middlewarePosition());
    }

    public function testHandler()
    {
        $this->assertEquals($this->middleware, $this->handler->handler());
    }
}
