<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Attribute;

use Slick\WebStack\Domain\Security\Attribute\IsGranted;
use PHPUnit\Framework\TestCase;

class IsGrantedTest extends TestCase
{

    public function testResponse(): void
    {
        $attribute = new IsGranted('test');
        $response = $attribute->response();
        $message = (string) $response->getBody();
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals("Access denied.", $message);
    }

    public function testMessage(): void
    {
        $attribute = new IsGranted('test', 'Test message');
        $this->assertEquals('Test message', $attribute->message());
    }

    public function testAttribute(): void
    {
        $attribute = new IsGranted('test');
        $this->assertEquals('test', $attribute->attribute());
    }

    public function testMessageAsJson(): void
    {
        $expected = json_encode([
            "jsonapi" => [
                "version" => "1.1",
            ],
            "errors" => [
                "code" => 403,
                "title" => "Forbidden",
                "detail" => "Access denied.",
            ]
        ]);
        $attribute = new IsGranted(attribute: 'test', asJson: true);
        $this->assertSame($expected, $attribute->message());
    }

    public function testRedirectResponse(): void
    {
        $attribute = new IsGranted(attribute: 'test', location: 'http://example.com/');
        $response = $attribute->response();
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals("http://example.com/", $response->getHeaderLine('Location'));
    }
}
