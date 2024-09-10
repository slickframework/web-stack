<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http\AuthenticationEntryPoint;

use Slick\WebStack\Domain\Security\Exception\AuthenticationException;
use Slick\WebStack\Domain\Security\Http\AuthenticationEntryPointInterface;
use Slick\WebStack\Infrastructure\Http\Authenticator\FormLoginAuthenticator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Response;
use Slick\Http\Session\SessionDriverInterface;

/**
 * FormLoginEntryPoint
 *
 * @package Slick\WebStack\Infrastructure\Http\AuthenticationEntryPoint
 */
final class FormLoginEntryPoint implements AuthenticationEntryPointInterface
{

    private FormLoginAuthenticator\FormLoginProperties $properties;

    public function __construct(
        private readonly SessionDriverInterface $sessionDriver,
        ?FormLoginAuthenticator\FormLoginProperties $properties = null
    ) {
        $this->properties = $properties ?? new FormLoginAuthenticator\FormLoginProperties([]);
    }

    /**
     * @inheritDoc
     */
    public function start(
        ServerRequestInterface $request,
        ?AuthenticationException $authException = null
    ): ResponseInterface {
        $path = $request->getUri()->getPath();
        if (!in_array($path, $this->properties->paths())) {
            $this->sessionDriver->set(
                FormLoginAuthenticator\LoginFormAuthenticatorHandler\RedirectHandler::LAST_URI,
                $request->getUri()->getPath()
            );
        }
        return new Response(status: 302, headers: ['Location' => $this->properties->path('login')]);
    }
}
