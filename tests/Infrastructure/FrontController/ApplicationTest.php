<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\FrontController;

use Features\App\UserInterface\CheckController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Response;
use Slick\Http\Message\Uri;
use Slick\WebStack\Infrastructure\FrontController\Application;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function runApplication(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/check-status'));
        $request->getMethod()->willReturn('GET');
        $request->withAttribute('route', Argument::type('array'))->willReturn($request);
        $request->getAttribute('route')->willReturn([
            "_controller" => CheckController::class,
            "_action" => "handle",
        ]);
        $app = new Application($request->reveal(), dirname(__DIR__, 3).'/features/app');
        $this->assertInstanceOf(ResponseInterface::class, $app->run());
    }

    #[Test]
    public function outputResponse(): void
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $app = new Application($request->reveal(), dirname(__DIR__, 3).'/features/app');
        $response = new Response(200, 'test', ['content-type' => 'text/plain']);
        $this->expectOutputString('test');
        $app->output($response);
    }
}
