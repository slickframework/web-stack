<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http\Server;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Template\Template;
use Slick\WebStack\Service\FlashMessages;

/**
 * Flash Messages Middleware
 *
 * @package Slick\WebStack\Http\Server
 */
class FlashMessagesMiddleware implements MiddlewareInterface
{
    /**
     * @var FlashMessages
     */
    private $flashMessages;

    /**
     * Creates a Flash Messages Middleware
     *
     * @param FlashMessages $flashMessages
     */
    public function __construct(FlashMessages $flashMessages)
    {
        Template::appendPath(dirname(dirname(dirname(__DIR__))).'/templates');
        $this->flashMessages = $flashMessages;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $request = $request->withAttribute(
            'viewData',
            $this->merge($request, ['flashMessages' => $this->flashMessages])
        );
        return $handler->handle($request);
    }

    /**
     * Merges any existing view data in the request with the provided one
     *
     * @param ServerRequestInterface $request
     * @param array                  $data
     *
     * @return array
     */
    private function merge(ServerRequestInterface $request, array $data)
    {
        $existing = $request->getAttribute('viewData', []);
        return array_merge($existing, $data);
    }
}
