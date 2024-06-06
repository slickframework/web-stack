<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Csrf\TokenGenerator;

use Slick\WebStack\Domain\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use PHPUnit\Framework\TestCase;

class UriSafeTokenGeneratorTest extends TestCase
{

    public function test__construct(): void
    {
        $generator = new UriSafeTokenGenerator();
        $this->assertInstanceOf(UriSafeTokenGenerator::class, $generator);
    }

    public function testGenerateToken(): void
    {
        $generator = new UriSafeTokenGenerator(50);
        $this->assertIsString($generator->generateToken());
    }

    public function testBadEntropy(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $generator = new UriSafeTokenGenerator(5);
        $this->assertNull($generator);
    }
}
