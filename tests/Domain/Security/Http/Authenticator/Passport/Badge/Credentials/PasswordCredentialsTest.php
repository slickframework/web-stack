<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials;

use Slick\WebStack\Domain\Security\Exception\LogicException;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials\PasswordCredentials;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\CredentialsInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PasswordCredentialsTest extends TestCase
{

    #[Test]
    public function initializable()
    {
        $passwordCredentials = new PasswordCredentials('password');
        $this->assertInstanceOf(PasswordCredentials::class, $passwordCredentials);
        $this->assertInstanceOf(CredentialsInterface::class, $passwordCredentials);
    }

    #[Test]
    public function hasAPassword()
    {
        $password = 'password';
        $passwordCredentials = new PasswordCredentials($password);
        $this->assertEquals($password, $passwordCredentials->password());
    }

    #[Test]
    public function resolvedState()
    {
        $password = 'password';
        $passwordCredentials = new PasswordCredentials($password);
        $this->assertFalse($passwordCredentials->isResolved());
        $passwordCredentials->markResolved();
        $this->assertTrue($passwordCredentials->isResolved());
    }

    #[Test]
    public function callAfterResolvedState()
    {
        $password = 'password';
        $passwordCredentials = new PasswordCredentials($password);
        $passwordCredentials->markResolved();
        $this->expectException(LogicException::class);
        $this->assertNotEquals($password, $passwordCredentials->password());
    }
}
