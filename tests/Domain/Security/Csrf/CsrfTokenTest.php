<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Csrf;

use Slick\WebStack\Domain\Security\Csrf\CsrfToken;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CsrfTokenTest extends TestCase
{

    #[Test]
    public function initializable(): void
    {
        $value = base64_encode(bin2hex(random_bytes(35)));
        $tokenId = 'my-token';
        $token = new CsrfToken($tokenId, $value);
        $this->assertInstanceOf(CsrfToken::class, $token);
        $this->assertEquals($tokenId, $token->tokenId());
        $this->assertEquals($value, $token->value());
        $this->assertInstanceOf(\Stringable::class, $token);
        $this->assertEquals($value, (string) $token);
    }
}
