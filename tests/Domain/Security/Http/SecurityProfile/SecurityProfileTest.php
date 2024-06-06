<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http\SecurityProfile;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorManagerInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\SecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfileInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

class SecurityProfileTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initialize()
    {
        $manager = $this->prophesize(AuthenticatorManagerInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $profile = new SecurityProfile('^/api', $manager->reveal(), $tokenStorage);
        $this->assertInstanceOf(SecurityProfile::class, $profile);
        $this->assertInstanceOf(SecurityProfileInterface::class, $profile);
    }

    #[Test]
    public function match(): void
    {
        $manager = $this->prophesize(AuthenticatorManagerInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $profile = new SecurityProfile('/^\/api(.*)/i', $manager->reveal(), $tokenStorage);
        $request = $this->prophesize(ServerRequestInterface::class);
        $uri = $this->prophesize(UriInterface::class);
        $uri->getPath()->willReturn('/api/test');
        $request->getUri()->willReturn($uri->reveal());
        $this->assertTrue($profile->match($request->reveal()));
    }

    #[Test]
    public function checkSupport()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $manager = $this->prophesize(AuthenticatorManagerInterface::class);
        $serverRequest = $request->reveal();
        $manager->supports($serverRequest)->willReturn(true)->shouldBeCalled();
        $manager->authenticateRequest($serverRequest)->shouldBeCalled()->willReturn(null);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();

        $profile = new SecurityProfile('/^\/api(.*)/i', $manager->reveal(), $tokenStorage);
        $this->assertNull($profile->process($serverRequest));
    }

    #[Test]
    public function noAuthenticatorsUnauthorized()
    {
        $request = $this->prophesize(ServerRequestInterface::class);
        $manager = $this->prophesize(AuthenticatorManagerInterface::class);
        $serverRequest = $request->reveal();
        $manager->supports($serverRequest)->willReturn(false)->shouldBeCalled();
        $manager->authenticateRequest($serverRequest)->shouldNotBeCalled();
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();

        $profile = new SecurityProfile('/^\/api(.*)/i', $manager->reveal(), $tokenStorage);

        $response = $profile->process($serverRequest);
        $this->assertEquals(401, $response->getStatusCode());
    }

    #[Test]
    public function noAuthenticatorsEntryStart()
    {
        $response = $this->prophesize(ResponseInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $entryPoint = $this->prophesize(AuthenticationEntryPointInterface::class);
        $serverRequest = $request->reveal();
        $entryPoint->start($serverRequest)->willReturn($response->reveal())->shouldBeCalled();
        $manager = $this->prophesize(AuthenticatorManagerInterface::class);
        $manager->supports($serverRequest)->willReturn(false)->shouldBeCalled();
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();

        $profile = new SecurityProfile('/^\/api(.*)/i', $manager->reveal(), $tokenStorage, $entryPoint->reveal());

        $processedResponse = $profile->process($serverRequest);
        $this->assertSame($response->reveal(), $processedResponse);
    }

    #[Test]
    public function authenticateResponse()
    {
        $response = $this->prophesize(ResponseInterface::class);
        $response->getStatusCode()->willReturn(401);
        $request = $this->prophesize(ServerRequestInterface::class);
        $manager = $this->prophesize(AuthenticatorManagerInterface::class);
        $serverRequest = $request->reveal();
        $manager->supports($serverRequest)->willReturn(true)->shouldBeCalled();
        $manager->authenticateRequest($serverRequest)->shouldBeCalled()->willReturn($response->reveal());
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();

        $profile = new SecurityProfile('/^\/api(.*)/i', $manager->reveal(), $tokenStorage);

        $this->assertSame($response->reveal(), $profile->process($serverRequest));
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

        $profile = new SecurityProfile('/^\/api(.*)/i', $manager->reveal(), $tokenStorage->reveal());

        $this->assertSame($response->reveal(), $profile->process($serverRequest));
    }

    #[Test]
    public function itHasAListOfErrors(): void
    {
        $manager = $this->prophesize(AuthenticatorManagerInterface::class);
        $errorList = ["Foo"];
        $manager->authenticationErrors()->willReturn($errorList);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class)->reveal();
        $profile = new SecurityProfile('^/api', $manager->reveal(), $tokenStorage);
        $this->assertSame($errorList, $profile->authenticationErrors());
    }
}
