<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Signature;

use Slick\WebStack\Domain\Security\Exception\ExpiredSignatureException;
use Slick\WebStack\Domain\Security\Exception\InvalidSignatureException;
use Slick\WebStack\Domain\Security\Signature\SignatureHasher;
use Slick\WebStack\Domain\Security\UserInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SignatureHasherTest extends TestCase
{
    use ProphecyTrait;

    private string $secret = "a2882e3ae10eb0e9c324b12ddad2eebb";

    #[Test]
    public function initializable(): void
    {
        $hasher = new SignatureHasher($this->secret);
        $this->assertInstanceOf(SignatureHasher::class, $hasher);
    }

    #[Test]
    public function computeSignatureHash(): void
    {
        $user = $this->prophesize(UserInterface::class);
        $user->userIdentifier()->willReturn("userIdentifier")->shouldBeCalled();
        $hasher = new SignatureHasher($this->secret);
        $hash = $hasher->computeSignatureHash($user->reveal(), time()+(60*60*24*30));
        $this->assertIsString($hash);
    }

    #[Test]
    public function verifySignatureHashExpired(): void
    {
        $user = $this->prophesize(UserInterface::class);
        $user->userIdentifier()->willReturn("userIdentifier");
        $hasher = new SignatureHasher($this->secret);
        $expires = time() - (60 * 60 * 24 * 31);
        $hash = $hasher->computeSignatureHash($user->reveal(), $expires);
        $this->expectException(ExpiredSignatureException::class);
        $hasher->verifySignatureHash($user->reveal(), $expires, $hash);
    }

    #[Test]
    public function verifySignatureHashInvalid(): void
    {
        $user = $this->prophesize(UserInterface::class);
        $user->userIdentifier()->willReturn("userIdentifier", "otherUser");
        $hasher = new SignatureHasher($this->secret);
        $expires = time() + (60 * 60 * 24 * 31);
        $hash = $hasher->computeSignatureHash($user->reveal(), $expires);
        $this->expectException(InvalidSignatureException::class);
        $hasher->verifySignatureHash($user->reveal(), $expires, $hash);
    }

    #[Test]
    public function acceptSignatureHash(): void
    {
        $user = $this->prophesize(UserInterface::class);
        $identifier = "userIdentifier";
        $user->userIdentifier()->willReturn("otherUser", $identifier);
        $hasher = new SignatureHasher($this->secret);
        $expires = time() + (60 * 60 * 24 * 31);
        $hash = $hasher->computeSignatureHash($user->reveal(), $expires);
        $this->expectException(InvalidSignatureException::class);
        $hasher->acceptSignatureHash($identifier, $expires, $hash);
    }

    #[Test]
    public function computeSignatureHashMultipleProperties(): void
    {
        $user = new DummyUser();
        $hasher = new SignatureHasher($this->secret, ['registeredOn', 'userId']);
        $expires = time() + (60 * 60 * 24 * 31);
        $hash = $hasher->computeSignatureHash($user, $expires);
        $this->assertIsString($hash);
    }

    #[Test]
    public function missingProperty(): void
    {
        $user = new DummyUser();
        $hasher = new SignatureHasher($this->secret, ['registeredOn', 'userId', 'city']);
        $expires = time() + (60 * 60 * 24 * 31);
        $this->expectException(InvalidSignatureException::class);
        $hasher->computeSignatureHash($user, $expires);
    }

    #[Test]
    public function nonScalarProperty(): void
    {
        $user = new DummyUser();
        $hasher = new SignatureHasher($this->secret, ['registeredOn', 'userId', 'email']);
        $expires = time() + (60 * 60 * 24 * 31);
        $this->expectException(InvalidSignatureException::class);
        $hasher->computeSignatureHash($user, $expires);
    }
}
