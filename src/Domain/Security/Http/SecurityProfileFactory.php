<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Di\ContainerInterface;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenValidator\UserIntegrityTokenValidator;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\DisabledSecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\SecurityProfile;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\SecurityProfileTrait;
use Slick\WebStack\Domain\Security\Http\SecurityProfile\StatefulSecurityProfile\SessionSecurityProfile;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\Factory\AuthenticatorsListFactory;

/**
 * Class SecurityProfileFactory
 */
class SecurityProfileFactory
{
    use SecurityProfileTrait;

    /**
     * @use ProfileFactoryTrait<UserInterface>
     */
    use ProfileFactoryTrait;

    protected ?SecurityProfileInterface $profile = null;

    private ?AuthenticationEntryPointInterface $entryPoint = null;

    /**
     * Creates a SecurityProfileFactory
     *
     * @param ContainerInterface $container
     */
    public function __construct(private readonly ContainerInterface $container)
    {
    }

    /**
     * Create a security profile based on a given profile configuration and request.
     *
     * @param array<string, mixed> $profileConfig The profile configuration.
     * @param ServerRequestInterface $request The server request.
     *
     * @return SecurityProfileInterface|null The created security profile, or null if no match found.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function createProfile(array $profileConfig, ServerRequestInterface $request): ?SecurityProfileInterface
    {
        foreach ($profileConfig['profiles'] as $profile) {
            if (!$this->matchExp = $profile['pattern'] ?? null) {
                continue;
            }

            if ($this->match($request)) {
                $this->profile = $this->createDisabledProfile($profile);
                return $this->profile;
            }
        }

        return null;
    }

    /**
     * Retrieves the security profile
     *
     * @return SecurityProfileInterface|null The security profile if available, null otherwise
     */
    public function profile(): ?SecurityProfileInterface
    {
        return $this->profile;
    }

    /**
     * Create a disabled security profile based on a given profile.
     *
     * @param array<string, mixed> $profile The profile configuration.
     * @return SecurityProfileInterface The created disabled security profile.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createDisabledProfile(array $profile): SecurityProfileInterface
    {
        $options = array_merge(self::$defaultOptions, $profile);

        if ($options['secured'] !== true) {
            if (isset($options['stateless']) && !$options['stateless']) {
                $tokenValidator = new UserIntegrityTokenValidator(
                    $this->createUserProvider(
                        $this->getProviderClass($profile)
                    )
                );

                return new DisabledSecurityProfile(
                    $options['pattern'],
                    $this->createTokenStorage($profile),
                    $this->createSessionDriver($profile),
                    $tokenValidator
                );
            }
            return new DisabledSecurityProfile($options['pattern']);
        }

        return $this->createStatelessProfile($options);
    }

    /**
     * Create a stateless security profile based on a given profile modules.
     *
     * @param array<string, mixed> $profile The profile configuration.
     * @return SecurityProfileInterface A stateless security profile, or null if the profile is not stateless.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createStatelessProfile(array $profile): SecurityProfileInterface
    {
        if ($profile['stateless'] === true) {
            return new SecurityProfile(
                $profile['pattern'],
                $this->createAuthenticatorManager($profile),
                $this->createTokenStorage($profile),
                $this->createEntryPoint($profile)
            );
        }

        return $this->createSecurityProfile($profile);
    }

    /**
     * Create a security profile based on a given profile configuration.
     *
     * @param array<string, mixed> $profile The profile configuration.
     * @return SecurityProfileInterface The created security profile.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createSecurityProfile(array $profile): SecurityProfileInterface
    {
        $tokenValidator = new UserIntegrityTokenValidator(
            $this->createUserProvider($this->getProviderClass($profile))
        );
        return new SessionSecurityProfile(
            $profile['pattern'],
            $this->createAuthenticatorManager($profile),
            $this->createTokenStorage($profile),
            $this->createSessionDriver($profile),
            $this->createEntryPoint($profile),
            $tokenValidator
        );
    }

    /**
     * Creates an authenticator manager instance based on a given profile.
     *
     * @param array<string, mixed> $profile The profile configuration
     *
     * @return AuthenticatorManagerInterface The authenticator manager instance
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createAuthenticatorManager(array $profile): AuthenticatorManagerInterface
    {
        $authenticators = new AuthenticatorsListFactory($this->container, $profile['authenticators']);
        $this->entryPoint = $authenticators->entryPoint();
        $args = [
            'authenticators' => $authenticators,
            'tokenStorage' => $this->createTokenStorage($profile),
            'hasher' => $this->createPasswordHasher($profile)
        ];
        return $this->container->make(AuthenticatorManager::class, ...array_values($args));
    }

    /**
     * Create an authentication entry point based on a given profile.
     *
     * @param array<string, mixed> $profile The profile configuration.
     * @return AuthenticationEntryPointInterface|null The created authentication entry point or null if
     *                                                no entry point is specified in the profile.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createEntryPoint(array $profile): ?AuthenticationEntryPointInterface
    {
        $config = array_merge(self::$defaultOptions, $profile);
        if ($config['entryPoint'] !== null) {
            return $this->container->get($config['entryPoint']);
        }

        return $this->entryPoint;
    }

    /**
     * @param array<string, mixed> $profile
     * @return string
     */
    private function getProviderClass(array $profile): string
    {
        return isset($profile['userProvider']) ? $profile['userProvider'] : UserProviderInterface::class;
    }
}
