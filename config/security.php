<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace config\services;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slick\Di\ContainerInterface;
use Slick\Di\Definition\ObjectDefinition;
use Slick\Http\Session\SessionDriverInterface;
use Slick\WebStack\Domain\Security\Authentication\Token\Storage\TokenStorage;
use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface as SessionTokenStorageInterface;
use Slick\WebStack\Domain\Security\AuthorizationCheckerInterface;
use Slick\WebStack\Domain\Security\Csrf\CsrfTokenManager;
use Slick\WebStack\Domain\Security\Csrf\CsrfTokenManagerInterface;
use Slick\WebStack\Domain\Security\Csrf\TokenGenerator\UriSafeTokenGenerator;
use Slick\WebStack\Domain\Security\Csrf\TokenStorage\SessionCsrfTokenStorage;
use Slick\WebStack\Domain\Security\Csrf\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Http\RememberMe\RememberMeHandlerInterface;
use Slick\WebStack\Domain\Security\Http\RememberMe\SignatureRememberMeHandler;
use Slick\WebStack\Domain\Security\Http\SecurityProfileFactory;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\Pbkdf2PasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\PhpPasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\PlaintextPasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use Slick\WebStack\Domain\Security\Security;
use Slick\WebStack\Domain\Security\SecurityAuthenticatorInterface;
use Slick\WebStack\Domain\Security\Signature\SignatureHasher;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use function Slick\ModuleApi\importSettingsFile;

$services = [];

$services[SecurityProfileFactory::class] = function (ContainerInterface $container) {
    return new SecurityProfileFactory($container);
};

$securityVariable = '@security';
$services[SecurityAuthenticatorInterface::class] = $securityVariable;
$services[AuthorizationCheckerInterface::class] = $securityVariable;
$services[Security::class] = $securityVariable;
$services['security'] = function (ContainerInterface $container) {
    $securityConfigPath = APP_ROOT . '/config/security.php';
    if (!is_file($securityConfigPath)) {
        file_put_contents($securityConfigPath, file_get_contents(__DIR__.'/default-security.settings.php'));
    }

    return new Security(
        $container->get(SecurityProfileFactory::class),
        $container->get('security.token.storage'),
        importSettingsFile($securityConfigPath),
        $container->get(SessionDriverInterface::class)
    );
};

$services[TokenStorageInterface::class] = '@security.token.storage';
$services['security.token.storage'] = ObjectDefinition::create(TokenStorage::class);

$services[RememberMeHandlerInterface::class] = function (ContainerInterface $container) {
    return new SignatureRememberMeHandler(
        $container->get(SignatureHasher::class),
        $container->get(UserProviderInterface::class),
        $container->get(ServerRequestInterface::class),
        $container->get('remember.me.cookie.options'),
        $container->get(LoggerInterface::class)
    );
};

//------------------------------------------------------------------
// Session storage
//------------------------------------------------------------------
$services[SessionTokenStorageInterface::class] = '@security.token.storage';

$envAppSecret = $_ENV["APP_SECRET"] ?? '';
//------------------------------------------------------------------
// Password hasher
//------------------------------------------------------------------
$services[PasswordHasherInterface::class] = '@password.hasher';
$services[PhpPasswordHasher::class] = '@password.hasher';
$services['password.hasher'] = function () {
    return new PhpPasswordHasher();
};
$services[Pbkdf2PasswordHasher::class] = fn() => new Pbkdf2PasswordHasher(salt: $envAppSecret);
$services[PlaintextPasswordHasher::class] = fn() => new PlaintextPasswordHasher();

$services[CsrfTokenManagerInterface::class] = function (ContainerInterface $container) {
    $session = $container->get(SessionDriverInterface::class);
    return new CsrfTokenManager(new SessionCsrfTokenStorage($session), new UriSafeTokenGenerator());
};

return $services;
