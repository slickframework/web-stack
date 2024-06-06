<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Authentication\Token;

use Slick\WebStack\Domain\Security\Authentication\Token\RememberMeToken;
use Slick\WebStack\Domain\Security\UserInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class RememberMeTokenTest extends TestCase
{
    use ProphecyTrait;

    private ?RememberMeToken $token = null;
    private ? string $secret = null;
    private $user;

    protected function setUp(): void
    {
        $usr = $this->prophesize(UserInterface::class);
        $usr->roles()->willReturn(['ROLE_USER']);
        $this->secret = 'secret';
        $this->user = $usr->reveal();
        $this->token = new RememberMeToken($this->user, $this->secret);
    }

    public function test__serialize()
    {
        $this->assertIsArray($this->token->__serialize());
    }

    public function testUser()
    {
        $this->assertSame($this->token->user(), $this->user);
    }

    public function testSecret()
    {
        $this->assertEquals($this->secret, $this->token->secret());
    }

    public function test__construct()
    {
        $this->assertInstanceOf(RememberMeToken::class, $this->token);
    }

    public function test__unserialize()
    {
        $token = new RememberMeToken(new TestUser(), $this->secret);
        $data = serialize($token);
        $unserializedToken = unserialize($data);
        $this->assertInstanceOf(RememberMeToken::class, $unserializedToken);
    }
}
