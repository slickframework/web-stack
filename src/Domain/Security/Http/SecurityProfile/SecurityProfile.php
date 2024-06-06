<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Domain\Security\Http\SecurityProfile;

use Slick\WebStack\Domain\Security\Authentication\Token\TokenStorageInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Domain\Security\Http\Authenticator\PassportInterface;
use Slick\WebStack\Domain\Security\Http\AuthenticatorManagerInterface;
use Slick\WebStack\Domain\Security\Http\SecurityProfileInterface;
use Slick\WebStack\Domain\Security\UserInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Response;

/**
 * SecurityProfile
 *
 * @package Slick\WebStack\Domain\Security\Http\SecurityProfile
 */
class SecurityProfile implements SecurityProfileInterface
{
    use SecurityProfileTrait;

    public const REQUEST_TOKEN_KEY = '_security_token';

    /**
     * Creates a SecurityProfile
     *
     * @param string $matchExp
     * @param AuthenticatorManagerInterface $authenticatorManager
     * @param TokenStorageInterface<UserInterface> $tokenStorage
     * @param AuthenticationEntryPointInterface|null $entryPoint
     */
    public function __construct(
        string $matchExp,
        private readonly AuthenticatorManagerInterface $authenticatorManager,
        protected readonly TokenStorageInterface $tokenStorage,
        private readonly ?AuthenticationEntryPointInterface $entryPoint = null
    ) {
        $this->matchExp = $matchExp;
    }

    /**
     * Processes the given server request by authenticating it or delegating to the entry point.
     *
     * @param ServerRequestInterface $request The server request to process.
     * @return ResponseInterface|null Returns the authenticated response or null if authentication is not supported.
     */
    public function process(ServerRequestInterface $request): ?ResponseInterface
    {
        $supports = $this->authenticatorManager->supports($request);
        if (!$supports) {
              return $this->processEntryPoint($request);
        }

        $response =  $this->authenticatorManager->authenticateRequest($request);
        if (is_null($response) || $response->getStatusCode() < 400) {
            $this->handleAuthenticationSuccess($request);
        }
        return $response;
    }

    /**
     * Processes the entry point for the given server request.
     *
     * If an entry point is defined, it will start the authentication process
     * using the provided server request. Otherwise, it returns a response with
     * a status code of 401 (Unauthorized).
     *
     * @param ServerRequestInterface $request The server request to process the entry point.
     * @return ResponseInterface Returns the response of the entry point or a response with
     *                           a status code of 401 (Unauthorized).
     */
    private function processEntryPoint(ServerRequestInterface $request): ResponseInterface
    {
        if ($this->entryPoint) {
            return $this->entryPoint->start($request);
        }

        return new Response(401, 'Unauthorized');
    }

    /**
     * Handles successful authentication by adding the token attribute to the request.
     *
     * @param ServerRequestInterface &$request The incoming request object.
     *
     * @return void
     */
    protected function handleAuthenticationSuccess(ServerRequestInterface &$request): void
    {
        $request = $request->withAttribute(self::REQUEST_TOKEN_KEY, $this->tokenStorage->getToken());
    }

    /**
     * @inheritDoc
     */
    
    public function authenticationErrors(): array
    {
        return $this->authenticatorManager->authenticationErrors();
    }
}
