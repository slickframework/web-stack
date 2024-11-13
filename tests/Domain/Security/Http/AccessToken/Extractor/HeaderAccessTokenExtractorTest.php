<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\AccessToken\Extractor;

use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Domain\Security\Http\AccessToken\Extractor\HeaderAccessTokenExtractor;
use PHPUnit\Framework\TestCase;

class HeaderAccessTokenExtractorTest extends TestCase
{

    public function testExtractAccessToken()
    {
        $extractor = new HeaderAccessTokenExtractor();
        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('hasHeader')->with('Authorization')->willReturn(true);
        $request->method('getHeaderLine')->with('Authorization')->willReturn('Bearer token-data');
        $this->assertEquals('token-data', $extractor->extractAccessToken($request));
    }
}
