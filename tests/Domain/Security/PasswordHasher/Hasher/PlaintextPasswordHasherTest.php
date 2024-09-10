<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\PasswordHasher\Hasher;

use Slick\WebStack\Domain\Security\Exception\InvalidPasswordException;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\PlaintextPasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PlaintextPasswordHasherTest extends TestCase
{

    #[Test]
    public function initializable()
    {
        $hasher = new PlaintextPasswordHasher();
        $this->assertInstanceOf(PlaintextPasswordHasher::class, $hasher);
    }

    #[Test]
    public function hashPassword()
    {
        $hasher = new PlaintextPasswordHasher();
        $password = 'password123';
        $hashedPassword = $hasher->hash($password, '!Td4');
        $this->assertEquals('password123{!Td4}', $hashedPassword);
    }

    #[Test]
    public function hashPasswordWithoutSalt()
    {
        $hasher = new PlaintextPasswordHasher();
        $password = 'password123';
        $hashedPassword = $hasher->hash($password);
        $this->assertEquals($password, $hashedPassword);
    }

    #[Test]
    public function invalidSaltCharacters()
    {
        $hasher = new PlaintextPasswordHasher();
        $password = 'password123';
        $this->expectException(\InvalidArgumentException::class);
        $hashedPassword = $hasher->hash($password, '!Td}4');
        $this->assertNull($hashedPassword);
    }

    #[Test]
    public function noNeedOfRehash()
    {
        $hasher = new PlaintextPasswordHasher();
        $password = 'what ever is the hashed password.';
        $this->assertFalse($hasher->needsRehash($password));
    }

    #[Test]
    public function passwordTooLong()
    {
        $password = $this->generateRandomString(PasswordHasherInterface::MAX_PASSWORD_LENGTH + 1);
        $hasher = new PlaintextPasswordHasher();
        $this->expectException(InvalidPasswordException::class);
        $hasher->hash($password);
    }

    #[Test]
    public function verifyPassword()
    {
        $hasher = new PlaintextPasswordHasher();
        $password = 'password123';
        $salt = '!Td4';
        $hashedPassword = $hasher->hash($password, $salt);
        $this->assertTrue($hasher->verify($hashedPassword, $password, $salt));
    }

    #[Test]
    public function longPasswordVerificationIsFalse()
    {
        $password = $this->generateRandomString(PasswordHasherInterface::MAX_PASSWORD_LENGTH + 1);
        $hasher = new PlaintextPasswordHasher();
        $this->assertFalse($hasher->verify("Whatever", $password, "Whatever"));
    }

    #[Test]
    public function info(): void
    {
        $hasher = new PlaintextPasswordHasher();
        $this->assertEquals([
            'algorithm' => 'plaintext'
        ], $hasher->info());
    }

    private function generateRandomString($nameLength = 8): string
    {
        $alphaNumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        return substr(str_shuffle(str_repeat($alphaNumeric, $nameLength)), 0, $nameLength);
    }
}
