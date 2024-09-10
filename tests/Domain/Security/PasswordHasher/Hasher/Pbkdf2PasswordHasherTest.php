<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\PasswordHasher\Hasher;

use Slick\WebStack\Domain\Security\Exception\InvalidPasswordException;
use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\Pbkdf2PasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class Pbkdf2PasswordHasherTest extends TestCase
{

    #[Test]
    public function needsRehashAlwaysFalse(): void
    {
        $hasher = new Pbkdf2PasswordHasher();
        $this->assertInstanceOf(Pbkdf2PasswordHasher::class, $hasher);
        $this->assertFalse($hasher->needsRehash('Whatever'));
    }

    #[Test]
    public function hashPassword(): void
    {
        $hasher = new Pbkdf2PasswordHasher();
        $plainPassword = 'password123';
        $salt = '908asd';
        $hasher->hash($plainPassword, $salt);
        $digest = hash_pbkdf2('sha512', $plainPassword, $salt ?? '', 1000, 40, true);
        $this->assertEquals(base64_encode($digest), $hasher->hash($plainPassword, $salt));
    }

    #[Test]
    public function passwordTooLong()
    {
        $hasher = new Pbkdf2PasswordHasher();
        $plainPassword = $this->generateRandomString(PasswordHasherInterface::MAX_PASSWORD_LENGTH + 2);
        $this->expectException(InvalidPasswordException::class);
        $hasher->hash($plainPassword);
    }

    #[Test]
    public function invalidAlgorithm()
    {
        $hasher = new Pbkdf2PasswordHasher(algorithm: 'invalid');
        $plainPassword = $this->generateRandomString(10);
        $this->expectException(LogicException::class);
        $hasher->hash($plainPassword);
    }

    #[Test]
    public function verifyPassword(): void
    {
        $hasher = new Pbkdf2PasswordHasher();
        $plainPassword = 'password123';
        $salt = '904r?"sd';
        $hashedPassword = $hasher->hash($plainPassword, $salt);
        $this->assertTrue($hasher->verify($hashedPassword, $plainPassword, $salt));
    }

    #[Test]
    public function verifyWrongLength()
    {
        $hasher = new Pbkdf2PasswordHasher();
        $plainPassword = 'password123';
        $salt = '904r?"sd';
        $hashedPassword = $hasher->hash($plainPassword, $salt);
        $this->assertFalse($hasher->verify($hashedPassword.' ', $plainPassword, $salt));
    }

    #[Test]
    public function hasherInfo(): void
    {
        $str = 'some-salt';
        $hasher = new Pbkdf2PasswordHasher(salt: $str);
        $this->assertEquals([
            'algorithm' => 'sha512',
            'iterations' => 1000,
            'length' => 56
        ], $hasher->info());
    }

    private function generateRandomString($nameLength = 8): string
    {
        $alphaNumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        return substr(str_shuffle(str_repeat($alphaNumeric, $nameLength)), 0, $nameLength);
    }
}
