<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfile;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenValidatorInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorManagerInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\SecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfile\SessionSecurityProfile;
use Slick\WebStack\Domain\Security\UserInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Session\SessionDriverInterface;

class SessionSecurityProfileTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $manager = $this->prophesize(AuthenticatorManagerInterface::class)->reveal();
        $entryPoint = $this->prophesize(AuthenticationEntryPointInterface::class)->reveal();
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $session = $this->prophesize(SessionDriverInterface::class)->reveal();

        $profile = new SessionSecurityProfile('^/api', $manager, $tokenStorage, $session, $entryPoint);

        $this->assertInstanceOf(SessionSecurityProfile::class, $profile);
        $this->assertInstanceOf(SecurityProfile::class, $profile);
    }

    #[Test]
    public function restoreToken()
    {
        $token = $this->prophesize(TokenInterface::class);
        $manager = $this->prophesize(AuthenticatorManagerInterface::class)->reveal();
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $session = $this->prophesize(SessionDriverInterface::class);
        $session->get(SessionSecurityProfile::SESSION_KEY)->willReturn($token->reveal(), null);
        $tokenStorage->setToken($token->reveal())->shouldBeCalled();

        $profile = new SessionSecurityProfile('^/api', $manager, $tokenStorage->reveal(), $session->reveal());

        $this->assertSame($token->reveal(), $profile->restoreToken());
        $this->assertNull($profile->restoreToken());
    }

    #[Test]
    public function authenticate200Response()
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(200);
        $token = $this->prophesize(TokenInterface::class);

        $request = $this->prophesize(ServerRequestInterface::class);
        $request->withAttribute(SecurityProfile::REQUEST_TOKEN_KEY, $token->reveal())->shouldBeCalled()->willReturn($request);
        $manager = $this->prophesize(AuthenticatorManagerInterface::class);
        $serverRequest = $request->reveal();
        $manager->supports($serverRequest)->willReturn(true)->shouldBeCalled();
        $manager->authenticateRequest($serverRequest)->shouldBeCalled()->willReturn($response->reveal());
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $tokenStorage->getToken()->shouldBeCalled()->willReturn($token);

        $session = $this->prophesize(SessionDriverInterface::class);
        $session->set(SessionSecurityProfile::SESSION_KEY, $token)->willReturn($session)->shouldBeCalled();

        $profile = new SessionSecurityProfile('/^\/api(.*)/i', $manager->reveal(), $tokenStorage->reveal(), $session->reveal());

        $this->assertSame($response->reveal(), $profile->process($serverRequest));
    }

    #[Test]
    public function tokenValidation()
    {
        $manager = $this->prophesize(AuthenticatorManagerInterface::class)->reveal();
        $entryPoint = $this->prophesize(AuthenticationEntryPointInterface::class)->reveal();
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $session = $this->prophesize(SessionDriverInterface::class);
        $validator = $this->prophesize(TokenValidatorInterface::class);
        $token = $this->prophesize(TokenInterface::class);

        $session->get(SessionSecurityProfile::SESSION_KEY)->willReturn($token->reveal());
        $validator->validate($token->reveal())->willReturn(false);

        $profile = new SessionSecurityProfile('^/api', $manager, $tokenStorage, $session->reveal(), $entryPoint, $validator->reveal());
        $this->assertNull($profile->restoreToken());
    }
}
