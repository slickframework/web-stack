<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http\Server;

use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Message\Stream\TextStream;
use Slick\Template\TemplateEngineInterface;
use Slick\WebStack\Renderer\ViewInflectorInterface;

/**
 * RendererMiddleware
 *
 * @package Slick\WebStack\Http\Server
 */
class RendererMiddleware implements MiddlewareInterface
{
    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * @var ViewInflectorInterface
     */
    private $viewInflector;

    /**
     * Creates a Renderer Middleware
     *
     * @param TemplateEngineInterface $templateEngine
     * @param ViewInflectorInterface $viewInflector
     */
    public function __construct(TemplateEngineInterface $templateEngine, ViewInflectorInterface $viewInflector)
    {
        $this->templateEngine = $templateEngine;
        $this->viewInflector = $viewInflector;
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
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        $response = $handler->handle($request);
        $body = new TextStream($this->createContent($request));
        $response = $response->withBody($body);
        return $response;
    }

    /**
     * Generates the content form request attributes
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function createContent(ServerRequestInterface $request)
    {
        $route = $request->getAttribute('route');
        $template = $this->viewInflector->inflect($route);
        $this->templateEngine->parse($template);
        return $this->templateEngine->process($request->getAttribute('viewData', []));
    }
}
