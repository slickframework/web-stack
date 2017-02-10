<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Http\SessionDriverInterface;

/**
 * Session Middleware
 *
 * @package Slick\WebStack\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class SessionMiddleware extends AbstractMiddleware implements MiddlewareInterface
{

    /**
     * @var SessionDriverInterface
     */
    private $sessionDriver;

    /**
     * Creates Session Middleware
     *
     * @param SessionDriverInterface $sessionDriver
     */
    public function __construct(SessionDriverInterface $sessionDriver)
    {
        $this->sessionDriver = $sessionDriver;
    }

    /**
     * Handles a Request and updated the response
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function handle(
        ServerRequestInterface $request, ResponseInterface $response
    )
    {
        $request = $request->withAttribute('session', $this->sessionDriver);
        return $this->executeNext($request, $response);
    }
}