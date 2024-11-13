<?php
/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Infrastructure\Http\Authenticator;

use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Domain\Security\Http\AccessToken\AccessTokenExtractorInterface;
use Slick\WebStack\Domain\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\AccessTokenAuthenticator;
use PHPUnit\Framework\TestCase;

class AccessTokenAuthenticatorTest extends TestCase
{

    protected ?AccessTokenAuthenticator $accessTokenAuthenticator = null;
    protected ?ServerRequestInterface $request = null;

    function setUp(): void
    {
        $extractor = $this->createMock(AccessTokenExtractorInterface::class);
        $handler = $this->createMock(AccessTokenHandlerInterface::class);
        $this->request = $this->createMock(ServerRequestInterface::class);
        $extractor->method('extractAccessToken')
            ->with($this->request)
            ->willReturn("Token-data");

        $this->accessTokenAuthenticator = new AccessTokenAuthenticator($extractor, $handler);
    }

    public function testSupports()
    {
        $this->assertTrue($this->accessTokenAuthenticator->supports($this->request));
    }
}
