<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator;

use Slick\Http\Session\SessionDriverInterface;
use Slick\WebStack\Domain\Security\Authentication\Token\UsernamePasswordToken;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\Authenticator\AuthenticatorHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\Credentials\PasswordCredentials;
use Slick\WebStack\Domain\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorInterface;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\User\UserProviderInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\FormLoginProperties;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator\LoginFormAuthenticatorHandler\RedirectHandler;

/**
 * FormLoginAuthenticator
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator
 * @template TUser of UserInterface
 * @implements AuthenticatorInterface<TUser>
 */
final class FormLoginAuthenticator implements AuthenticatorInterface
{

    use AuthenticatorHandlerTrait;

    private FormLoginProperties $properties;

    /**
     * Creates a FormLoginAuthenticator
     *
     * @param UserProviderInterface<TUser> $provider
     * @param AuthenticatorHandlerInterface $handler
     * @param SessionDriverInterface $session
     * @param FormLoginProperties|null $properties
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        private readonly UserProviderInterface  $provider,
        AuthenticatorHandlerInterface           $handler,
        private readonly SessionDriverInterface $session,
        ?FormLoginProperties                    $properties = null,
        private readonly ?LoggerInterface       $logger = null,
    ) {
        $this->properties = $properties ?? new FormLoginProperties([]);
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function supports(ServerRequestInterface $request): ?bool
    {
        $path = $request->getUri()->getPath();
        if (in_array($path, $this->properties->paths())) {
            return true;
        }

        $payload = $request->getParsedBody();
        $isInvalidPayload = !isset($payload[$this->properties->parameter('username')])
            || !isset($payload[$this->properties->parameter('password')]);

        $isNotPostMethod = $request->getMethod() !== "POST";
        $isNotCheckPath = $path !== $this->properties->path('check');

        return !($isNotPostMethod || $isNotCheckPath || $isInvalidPayload);
    }

    /**
     * @inheritDoc
     */
    public function authenticate(ServerRequestInterface $request): PassportInterface
    {
        list($userName, $password) = $this->parseCredentials($request);

        $userBadge = new UserBadge($userName, $this->provider);
        $credentials = new PasswordCredentials($password);

        return $this->handler->onAuthenticate($request, new Passport($userBadge, $credentials));
    }

    /**
     * @inheritDoc
     * @return TokenInterface<UserInterface>
     * @throws SecurityException
     */
    public function createToken(PassportInterface $passport): TokenInterface
    {
        $authToken = new UsernamePasswordToken($passport->user(), $passport->user()->roles());
        $authToken->withAttributes([
            'IS_AUTHENTICATED_FULLY' => 'true',
            'IS_AUTHENTICATED_REMEMBERED' => 'false',
            'IS_AUTHENTICATED' => 'true'
        ]);
        return $authToken;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(ServerRequestInterface $request, TokenInterface $token): ?ResponseInterface
    {
        return $this->handler->onAuthenticationSuccess($request, $token);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(
        ServerRequestInterface $request,
        AuthenticationException $exception
    ): ?ResponseInterface {

        if ($request->getMethod() !== "POST") {
            if ($this->properties->useReferer()) {
                $referer = parse_url($request->getHeaderLine('referer'), PHP_URL_PATH);
                if (is_string($referer) && !in_array($referer, $this->properties->paths())) {
                    $this->session->get(RedirectHandler::LAST_URI, $referer);
                }
            }

            return null;
        }

        list($userName) = $this->parseCredentials($request);
        $this->logger?->info(
            'Authentication failed for user.',
            [
                'username' => $userName,
                'exception' => $exception,
            ]
        );

        return $this->handler->onAuthenticationFailure($request, $exception);
    }


    /**
     * @param ServerRequestInterface $request
     * @return string[]
     */
    private function parseCredentials(ServerRequestInterface $request): array
    {
        $parsedBody = $request->getParsedBody();
        if (!is_array($parsedBody)) {
            $code = $request->getMethod() !== "POST";
            throw new AuthenticationException(
                message: "The post data has no valid credentials. Please check your form field names in ".
                "your security configuration and try again.",
                code: $code ? 1 : 0
            );
        }

        $userName = $parsedBody[$this->properties->parameter('username')] ?? '';
        $password = $parsedBody[$this->properties->parameter('password')] ?? '';

        return array($userName, $password);
    }

    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        // Do nothing
    }
}
