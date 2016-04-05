<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Mvc\Application;

/**
 * Session initialization
 * 
 * @package Slick\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Session extends AbstractMiddleware implements MiddlewareInterface
{

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
        $session = Application::container()->get('session');
        $request = $request->withAttribute('session', $session);
        return $this->executeNext($request, $response);
    }
}