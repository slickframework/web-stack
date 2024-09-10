<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http;

use Slick\WebStack\Domain\Security\Http\HttpStatusCode;
use PHPUnit\Framework\TestCase;

class HttpStatusCodeTest extends TestCase
{

    public function testDescription(): void
    {
        $status = new HttpStatusCode(200);
        $this->assertEquals('OK', $status->description());
    }

    public function testConstruct(): void
    {
        $status = new HttpStatusCode(620);
        $this->assertEquals(620, $status->code());
        $this->assertEquals("Unknown HTTP status code: 620", $status->description());
    }

    public function testCode(): void
    {
        $status = new HttpStatusCode(200);
        $this->assertEquals(200, $status->code());
    }
}
