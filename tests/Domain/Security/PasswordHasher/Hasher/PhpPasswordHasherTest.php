<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\PasswordHasher\Hasher;

use Slick\WebStack\Domain\Security\Exception\InvalidPasswordException;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\PhpPasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PhpPasswordHasherTest extends TestCase
{

    #[Test]
    public function initializable()
    {
        $hasher = new PhpPasswordHasher();
        $this->assertInstanceOf(PhpPasswordHasher::class, $hasher);
    }

    #[Test]
    public function chooseAlgorithm()
    {
        $hasher = new PhpPasswordHasher(algorithm: PASSWORD_ARGON2ID);
        $this->assertInstanceOf(PhpPasswordHasher::class, $hasher);
    }

    #[Test]
    public function invalidOpsLimit()
    {
        $this->expectException(InvalidArgumentException::class);
        $hasher = new PhpPasswordHasher(opsLimit: 2);
        $this->assertNull($hasher);
    }

    #[Test]
    public function invalidMemLimit()
    {
        $this->expectException(InvalidArgumentException::class);
        $hasher = new PhpPasswordHasher(memLimit: 200);
        $this->assertNull($hasher);
    }

    #[Test]
    public function invalidCost()
    {
        $this->expectException(InvalidArgumentException::class);
        $hasher = new PhpPasswordHasher(cost: 200);
        $this->assertNull($hasher);
    }

    #[Test]
    public function hashPassword()
    {
        $hasher = new PhpPasswordHasher();
        $this->assertNotEmpty($hasher->hash('password'));
    }

    #[Test]
    public function longPasswordHash()
    {
        $hasher = new PhpPasswordHasher(algorithm: PASSWORD_BCRYPT);
        $this->assertNotEmpty($hasher->hash($this->generateRandomString(80)));
    }

    #[Test]
    public function longPassword()
    {
        $this->expectException(InvalidPasswordException::class);
        $hasher = new PhpPasswordHasher();
        $this->assertNull($hasher->hash($this->generateRandomString(PasswordHasherInterface::MAX_PASSWORD_LENGTH + 2)));
    }

    #[Test]
    public function verifyPassword()
    {
        $hasher = new PhpPasswordHasher(algorithm: PASSWORD_ARGON2I);
        $hashedPassword = $hasher->hash('password23');
        $this->assertTrue($hasher->verify($hashedPassword, 'password23'));
    }

    #[Test]
    public function info(): void
    {
        $hasher = new PhpPasswordHasher(algorithm: PASSWORD_ARGON2I);
        $this->assertEquals([
            'algorithm' => 'Argon 2I',
            'cost' => 13,
            'time_cost' => 4,
            'memory_cost' => 65536,
            'threads' => 1
        ], $hasher->info());
    }

    #[Test]
    public function verifyLongPasswords()
    {
        $hasher = new PhpPasswordHasher(algorithm: PASSWORD_BCRYPT);
        $plainPassword = $this->generateRandomString(84);
        $hashedPassword = $hasher->hash($plainPassword);
        $this->assertTrue($hasher->verify($hashedPassword, $plainPassword));
    }

    #[Test]
    public function verifyVeryLongPasswords()
    {
        $hasher = new PhpPasswordHasher();
        $plainPassword = $this->generateRandomString(PasswordHasherInterface::MAX_PASSWORD_LENGTH + 2);
        $hashedPassword = $hasher->hash('test');
        $this->assertFalse($hasher->verify($hashedPassword, $plainPassword));
    }

    #[Test]
    public function checkNeedsRehash()
    {
        $hasher = new PhpPasswordHasher(algorithm: PASSWORD_ARGON2I);
        $hashedPassword = $hasher->hash('password23');
        $this->assertFalse($hasher->needsRehash($hashedPassword));
    }

    private function generateRandomString($nameLength = 8): string
    {
        $alphaNumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        return substr(str_shuffle(str_repeat($alphaNumeric, $nameLength)), 0, $nameLength);
    }
}
