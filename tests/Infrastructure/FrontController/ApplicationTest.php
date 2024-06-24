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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Infrastructure\FrontController\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function runApplication(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class)->reveal();
        $app = new Application($request, dirname(__DIR__, 3).'/features/app');
        $this->assertInstanceOf(ResponseInterface::class, $app->run());
    }
}
