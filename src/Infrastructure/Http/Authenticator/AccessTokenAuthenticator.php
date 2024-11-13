<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\Authenticator;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Response;
use Slick\WebStack\Domain\Security\Authentication\Token\UserToken;
use Slick\WebStack\Domain\Security\Authentication\TokenInterface;
use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Exception\BadCredentialsException;
use Slick\WebStack\Domain\Security\Http\AccessToken\AccessTokenExtractorInterface;
use Slick\WebStack\Domain\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\SelfValidatingPassport;
use Slick\WebStack\Domain\Security\Http\AuthenticatorInterface;
use Slick\WebStack\Domain\Security\SecurityException;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * AccessTokenAuthenticator
 *
 * @package Slick\WebStack\Infrastructure\Http\Authenticator
 * @implements AuthenticatorInterface<UserInterface>
 */
final class AccessTokenAuthenticator implements AuthenticatorInterface
{

    use AuthenticatorHandlerTrait;

    /**
     * Creates a AccessTokenAuthenticator
     *
     * @param AccessTokenExtractorInterface $extractor
     * @param AccessTokenHandlerInterface $tokenHandler
     */
    public function __construct(
        private readonly AccessTokenExtractorInterface $extractor,
        private readonly AccessTokenHandlerInterface   $tokenHandler
    ) {
    }

    /**
     * @inheritDoc
     */
    public function supports(ServerRequestInterface $request): bool
    {
        return is_string($this->extractor->extractAccessToken($request));
    }

    /**
     * @inheritDoc
     */
    public function authenticate(ServerRequestInterface $request): PassportInterface
    {
        $accessToken = $this->extractor->extractAccessToken($request);
        if (null === $accessToken) {
            throw new BadCredentialsException("Invalid credentials");
        }

        $userBadge = $this->tokenHandler->userBadgeFromToken($accessToken);
        return new SelfValidatingPassport($userBadge);
    }

    /**
     * @inheritDoc
     * @throws SecurityException
     */
    public function createToken(PassportInterface $passport): TokenInterface
    {
        $userToken = new UserToken($passport->user());
        $userToken->withAttributes([
            'IS_AUTHENTICATED_FULLY' => 'true',
            'IS_AUTHENTICATED_REMEMBERED' => 'false',
            'IS_AUTHENTICATED' => 'true'
        ]);
        return $userToken;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(ServerRequestInterface $request, TokenInterface $token): ?ResponseInterface
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(
        ServerRequestInterface $request,
        AuthenticationException $exception
    ): ResponseInterface {
        return new Response(
            401,
            'Unauthorized',
            ['WWW-Authenticate' => $this->getAuthenticateHeader($exception->getMessage())]
        );
    }


    /**
     * @inheritDoc
     */
    public function clear(): void
    {
        // noop
    }

    /**
     * @see https://datatracker.ietf.org/doc/html/rfc6750#section-3
     */
    private function getAuthenticateHeader(?string $errorDescription = null): string
    {
        $data = [
            'error' => 'invalid_token',
            'error_description' => $errorDescription,
        ];
        $values = [];
        foreach ($data as $k => $v) {
            if (null === $v || '' === $v) {
                continue;
            }
            $values[] = sprintf('%s="%s"', $k, $v);
        }

        return sprintf('Bearer %s', implode(',', $values));
    }
}
