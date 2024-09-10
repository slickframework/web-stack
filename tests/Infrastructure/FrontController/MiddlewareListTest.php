<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\FrontController;

use PHPUnit\Framework\Attributes\Test;
use Prophecy\PhpUnit\ProphecyTrait;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandlerInterface;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewarePosition;
use Slick\ModuleApi\Infrastructure\FrontController\Position;
use Slick\WebStack\Infrastructure\FrontController\MiddlewareList;
use PHPUnit\Framework\TestCase;

class MiddlewareListTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function isInitializable(): void
    {
        $middlewareList = new MiddlewareList();
        $this->assertInstanceOf(MiddlewareList::class, $middlewareList);
    }

    #[Test]
    public function addMiddlewareOnTop(): void
    {
        $handlerTop = $this->prophesize(MiddlewareHandlerInterface::class);
        $handlerBottom = $this->prophesize(MiddlewareHandlerInterface::class);

        $handlerTop->middlewarePosition()->willReturn(new MiddlewarePosition(Position::Top));
        $handlerTop->name()->willReturn('top');
        $handlerBottom->middlewarePosition()->willReturn(new MiddlewarePosition(Position::Bottom));
        $handlerBottom->name()->willReturn('bottom');

        $middlewareList = new MiddlewareList();
        $middlewareList
            ->add($handlerBottom->reveal())
            ->add($handlerTop->reveal())
        ;

        $this->assertSame($handlerTop->reveal(), $middlewareList->getIterator()->first());
    }

    #[Test]
    public function addBefore(): void
    {
        $handlerTop = $this->prophesize(MiddlewareHandlerInterface::class);
        $handlerBottom = $this->prophesize(MiddlewareHandlerInterface::class);

        $handlerTop->middlewarePosition()->willReturn(new MiddlewarePosition(Position::Top));
        $handlerTop->name()->willReturn('top');
        $handlerBottom->middlewarePosition()->willReturn(new MiddlewarePosition(Position::Bottom));
        $handlerBottom->name()->willReturn('bottom');

        $handlerMiddle = $this->prophesize(MiddlewareHandlerInterface::class);
        $handlerMiddle->middlewarePosition()->willReturn(new MiddlewarePosition(Position::Before, 'bottom'));
        $handlerMiddle->name()->willReturn('middle');

        $middlewareList = new MiddlewareList();
        $middlewareList
            ->add($handlerBottom->reveal())
            ->add($handlerTop->reveal())
            ->add($handlerMiddle->reveal())
        ;

        $this->assertSame($handlerMiddle->reveal(), $middlewareList->getIterator()->next());
    }

    #[Test]
    public function addAfter(): void
    {
        $handlerTop = $this->prophesize(MiddlewareHandlerInterface::class);
        $handlerBottom = $this->prophesize(MiddlewareHandlerInterface::class);

        $handlerTop->middlewarePosition()->willReturn(new MiddlewarePosition(Position::Top));
        $handlerTop->name()->willReturn('top');
        $handlerBottom->middlewarePosition()->willReturn(new MiddlewarePosition(Position::Bottom));
        $handlerBottom->name()->willReturn('bottom');

        $handlerMiddle = $this->prophesize(MiddlewareHandlerInterface::class);
        $handlerMiddle->middlewarePosition()->willReturn(new MiddlewarePosition(Position::After, 'top'));
        $handlerMiddle->name()->willReturn('middle');

        $middlewareList = new MiddlewareList();
        $middlewareList
            ->add($handlerBottom->reveal())
            ->add($handlerTop->reveal())
            ->add($handlerMiddle->reveal())
        ;

        $this->assertSame($handlerMiddle->reveal(), $middlewareList->getIterator()->next());
    }
}
