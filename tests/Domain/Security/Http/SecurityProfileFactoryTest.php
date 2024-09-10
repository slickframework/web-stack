<?php
/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Test\Slick\WebStack\Domain\Security\Http;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorManager;
use Slick\WebStack\Domain\Security\Http\AuthenticatorManagerInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\DisabledSecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\SecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfileFactory;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\PhpPasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\HttpBasicAuthenticator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Di\ContainerInterface;
use Slick\Http\Message\Uri;
use Slick\Http\Session\SessionDriverInterface;

class SecurityProfileFactoryTest extends TestCase
{
    use ProphecyTrait;

    #[Test]
    public function initializable()
    {
        $container = $this->prophesize(ContainerInterface::class);
        $factory = new SecurityProfileFactory($container->reveal());
        $this->assertInstanceOf(SecurityProfileFactory::class, $factory);
    }

    #[Test]
    public function createDisableProfile()
    {
        $profiles = [
            'profiles' => [
                'all' => [
                    'pattern' => '/(.*)/i',
                    'secured' => false,
                ]
            ]
        ];
        $container = $this->prophesize(ContainerInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));
        $factory = new SecurityProfileFactory($container->reveal());
        $this->assertInstanceOf(DisabledSecurityProfile::class, $factory->createProfile($profiles, $request->reveal()));
    }

    #[Test]
    public function noPattern()
    {
        $profiles = [
            'profiles' => [
                'all' => [
                    'secured' => false,
                ]
            ]
        ];
        $container = $this->prophesize(ContainerInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));
        $factory = new SecurityProfileFactory($container->reveal());
        $this->assertNull($factory->createProfile($profiles, $request->reveal()));
    }

    #[Test]
    public function noMatchProfiles()
    {
        $profiles = [
            'profiles' => [
                'all' => [
                    'pattern' => '/test\/(.*)/i',
                    'secured' => false,
                ]
            ]
        ];
        $container = $this->prophesize(ContainerInterface::class);
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));
        $factory = new SecurityProfileFactory($container->reveal());
        $this->assertNull($factory->createProfile($profiles, $request->reveal()));
    }

    #[Test]
    public function createSecurityProfile()
    {
        $providerKey = 'provider-class-name-or-key';
        $profiles = [
            'profiles' => [
                'all' => [
                    'pattern' => '/(.*)/i',
                    'secured' => true,
                    'userProvider' => $providerKey,
                    'authenticators' => [
                        'httpBasicAuth' => [
                            'realm' => 'realm',
                        ]
                    ]
                ]
            ]
        ];
        $container = $this->prophesize(ContainerInterface::class);
        $provider = $this->prophesize(UserProviderInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $hasher = $this->prophesize(PasswordHasherInterface::class);
        $container->get($providerKey)->willReturn($provider->reveal());
        $container->get(TokenStorageInterface::class)->willReturn($tokenStorage->reveal());
        $container->get(PhpPasswordHasher::class)->willReturn($hasher->reveal());
        $arg = Argument::any();
        $authenticator = $this->prophesize(AuthenticatorManagerInterface::class);
        $authenticator->willImplement(AuthenticationEntryPointInterface::class);
        $container->make(AuthenticatorManager::class, ...array_values([$arg, $arg, $arg]))->willReturn($authenticator->reveal());
        $container->make(HttpBasicAuthenticator::class, ...array_values(['realm']))->willReturn($authenticator->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));
        $factory = new SecurityProfileFactory($container->reveal());
        $this->assertInstanceOf(SecurityProfile::class, $factory->createProfile($profiles, $request->reveal()));
    }

    #[Test]
    public function createStatefulSecurityProfile()
    {
        $providerKey = 'provider-class-name-or-key';
        $entryPointKey = 'entry-point-key';
        $profiles = [
            'profiles' => [
                'all' => [
                    'pattern' => '/(.*)/i',
                    'secured' => true,
                    'userProvider' => $providerKey,
                    'stateless' => false,
                    'authenticators' => [
                        'httpBasicAuth' => [
                            'realm' => 'realm',
                        ]
                    ],
                    'entryPoint' => $entryPointKey
                ]
            ]
        ];
        $container = $this->prophesize(ContainerInterface::class);
        $provider = $this->prophesize(UserProviderInterface::class);
        $tokenStorage = $this->prophesize(TokenStorageInterface::class);
        $hasher = $this->prophesize(PasswordHasherInterface::class);
        $session = $this->prophesize(SessionDriverInterface::class);
        $entryPoint = $this->prophesize(AuthenticationEntryPointInterface::class);
        $container->get($providerKey)->willReturn($provider->reveal());
        $container->get(TokenStorageInterface::class)->willReturn($tokenStorage->reveal());
        $container->get(PhpPasswordHasher::class)->willReturn($hasher->reveal());
        $container->get(SessionDriverInterface::class)->willReturn($session->reveal());
        $container->get(SessionDriverInterface::class)->willReturn($session->reveal());
        $container->get($entryPointKey)->willReturn($entryPoint->reveal());
        $arg = Argument::any();
        $authenticator = $this->prophesize(AuthenticatorManagerInterface::class);
        $authenticator->willImplement(AuthenticationEntryPointInterface::class);
        $container->make(AuthenticatorManager::class, ...array_values([$arg, $arg, $arg]))->willReturn($authenticator->reveal());
        $container->make(HttpBasicAuthenticator::class, ...array_values(['realm']))->willReturn($authenticator->reveal());
        $request = $this->prophesize(ServerRequestInterface::class);
        $request->getUri()->willReturn(new Uri('http://example.com/'));
        $factory = new SecurityProfileFactory($container->reveal());
        $this->assertInstanceOf(SecurityProfile::class, $factory->createProfile($profiles, $request->reveal()));
    }
}
