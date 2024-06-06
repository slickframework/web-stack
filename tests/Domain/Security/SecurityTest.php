<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\AuthorizationCheckerInterface;
use Slick\WebStack\Domain\Security\Exception\UserNotFoundException;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfileInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfileFactory;
use Slick\WebStack\Domain\Security\Http\SecurityProfileInterface;
use Slick\WebStack\Domain\Security\Security;
use Slick\WebStack\Domain\Security\SecurityAuthenticatorInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Uri;

class SecurityTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $config = [];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);
        $this->assertInstanceOf(Security::class, $security);
        $this->assertInstanceOf(SecurityAuthenticatorInterface::class, $security);
        $this->assertInstanceOf(AuthorizationCheckerInterface::class, $security);
        $this->assertFalse($security->enabled());
    }

    #[Test]
    public function initializeEnable()
    {
        $acl = ['/\/?(.*)/i' => ['IS_AUTHENTICATED', 'ROLE_USER']];
        $config = ['enabled' => true, 'accessControl' => $acl];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);
        $this->assertTrue($security->enabled());
        $this->assertEquals($acl, $security->acl());
    }

    #[Test]
    public function itHasAUser()
    {
        $config = ['enabled' => true];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $token = $this->prophesize(TokenInterface::class);
        $user = $this->prophesize(UserInterface::class);
        $token->user()->willReturn($user->reveal());
        $tokenStorage->getToken()->willReturn($token->reveal());
        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);
        $this->assertSame($user->reveal(), $security->authenticatedUser());
    }

    #[Test]
    public function missingTokenUser(): void
    {
        $config = ['enabled' => true];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $token = $this->prophesize(TokenInterface::class);
        $token->user()->willReturn(null);
        $tokenStorage->getToken()->willReturn($token->reveal());

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->expectException(UserNotFoundException::class);
        $security->authenticatedUser();
    }

    #[Test]
    public function processWithoutProfile()
    {
        $config = ['enabled' => true, 'accessControl' => []];
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $factory->createProfile($config, $request->reveal())->willReturn(null);
        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertNull($security->process($request->reveal()));
    }

    #[Test]
    public function processValidStatefulProfile(): void
    {
        $config = ['enabled' => true, 'accessControl' => []];
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $token = $this->prophesize(TokenInterface::class);
        $profile = $this->prophesize(StatefulSecurityProfileInterface::class);
        $profile->restoreToken()->willReturn($token->reveal());
        $factory->createProfile($config, $request->reveal())->willReturn($profile->reveal());
        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertNull($security->process($request->reveal()));
    }

    #[Test]
    public function processValidProfile(): void
    {
        $config = ['enabled' => true, 'accessControl' => []];
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $profile = $this->prophesize(SecurityProfileInterface::class);
        $profile->process($request->reveal())->willReturn(null);
        $profile->authenticationErrors()->willReturn([]);
        $factory->createProfile($config, $request->reveal())->willReturn($profile->reveal());
        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertNull($security->process($request->reveal()));
    }

    #[Test]
    public function grantRole(): void
    {
        $config = ['enabled' => true];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $token = $this->prophesize(TokenInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->getToken()->willReturn($token->reveal());
        $token->roleNames()->willReturn(['ROLE_USER']);

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertTrue($security->isGranted('ROLE_USER'));
    }

    #[Test]
    public function grantListOfAttributes(): void
    {
        $config = ['enabled' => true];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $token = $this->prophesize(TokenInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->getToken()->willReturn($token->reveal());
        $token->roleNames()->willReturn(['ROLE_USER']);

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertTrue($security->isGranted(['ROLE_USER', 'IS_AUTHENTICATED']));
    }

    #[Test]
    public function listOfAttributesFails(): void
    {
        $config = ['enabled' => true];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $token = $this->prophesize(TokenInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->getToken()->willReturn($token->reveal());
        $token->attributes()->willReturn([]);
        $token->roleNames()->willReturn(['ROLE_USER']);

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertFalse($security->isGranted(['ROLE_ADMIN', 'IS_AUTHENTICATED']));
    }

    #[Test]
    public function noTokenShouldNotGrant(): void
    {
        $config = ['enabled' => true];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->getToken()->willReturn(null);

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertFalse($security->isGranted('ROLE_USER'));
    }

    #[Test]
    public function grantAttribute(): void
    {
        $config = ['enabled' => true];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $token = $this->prophesize(TokenInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->getToken()->willReturn($token->reveal());
        $token->roleNames()->willReturn(['ROLE_USER']);
        $token->attributes()->willReturn(['IS_AUTHENTICATED' => true]);

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertTrue($security->isGranted('IS_AUTHENTICATED'));
    }

    #[Test]
    public function doNotGrantMissingAttribute(): void
    {
        $config = ['enabled' => true];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $token = $this->prophesize(TokenInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->getToken()->willReturn($token->reveal());
        $token->roleNames()->willReturn(['ROLE_USER']);
        $token->attributes()->willReturn(['IS_AUTHENTICATED' => true]);

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertFalse($security->isGranted('IS_AUTHENTICATED_FULLY'));
    }

    #[Test]
    public function grantAcl()
    {
        $acl = ['/\/?(.*)/i' => ['IS_AUTHENTICATED', 'ROLE_USER']];
        $config = ['enabled' => true, 'accessControl' => $acl];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $token = $this->prophesize(TokenInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->getToken()->willReturn($token->reveal());
        $token->roleNames()->willReturn(['ROLE_USER']);
        $token->attributes()->willReturn([]);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/test'));

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertTrue($security->isGrantedAcl($request->reveal()));
    }

    #[Test]
    public function grantAclTrueOnNoRequestMatch()
    {
        $acl = ['/\/other\/(.*)/i' => ['IS_AUTHENTICATED', 'ROLE_USER']];
        $config = ['enabled' => true, 'accessControl' => $acl];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $token = $this->prophesize(TokenInterface::class);
        $tokenStorage->getToken()->willReturn($token->reveal());

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/test'));

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertTrue($security->isGrantedAcl($request->reveal()));
    }

    #[Test]
    public function grantAclTrueOnNoToken()
    {
        $acl = ['/\/test(.*)/i' => ['IS_AUTHENTICATED', 'ROLE_USER']];
        $config = ['enabled' => true, 'accessControl' => $acl];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->getToken()->willReturn(null);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/test'));

        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);

        $this->assertTrue($security->isGrantedAcl($request->reveal()));
    }

    #[Test]
    public function itHasAListOfErrors()
    {
        $config = [];
        $factory = $this->prophesize(SecurityProfileFactory::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $security = new Security($factory->reveal(), $tokenStorage->reveal(), $config);
        $this->assertEmpty($security->authenticationErrors());
    }
}
