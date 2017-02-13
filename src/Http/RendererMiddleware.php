<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Http\Stream;
use Slick\WebStack\Http\Renderer\ViewInflectorInterface;
use Slick\Template\TemplateEngineInterface;

/**
 * Renderer Middleware
 *
 * @package Slick\WebStack\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
final class RendererMiddleware extends AbstractMiddleware implements
    MiddlewareInterface
{
    /**
     * @var TemplateEngineInterface
     */
    private $templateEngine;

    /**
     * @var ViewInflectorInterface
     */
    private $inflector;

    /**
     * Creates a renderer middleware
     *
     * @param TemplateEngineInterface $templateEngine
     * @param ViewInflectorInterface $inflector
     */
    public function __construct(
        TemplateEngineInterface $templateEngine,
        ViewInflectorInterface $inflector
    ) {
        $this->templateEngine = $templateEngine;
        $this->inflector = $inflector;
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
    ) {
        if ($response->getStatusCode() !== 302) {
            $content = $this->getContent($request);
            $response = $this->writeContent($response, $content);
        }

        return $this->executeNext($request, $response);
    }

    /**
     * Get content from template processing
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    private function getContent(ServerRequestInterface $request)
    {
        $templateFile = $this->inflector
            ->inflect($request->getAttribute('route'));
        $data = $request->getAttribute('viewData', []);
        return $this->templateEngine
            ->parse($templateFile)
            ->process($data);
    }

    /**
     * Write content to the resulting response
     *
     * @param ResponseInterface $response
     * @param string            $content
     *
     * @return ResponseInterface
     */
    private function writeContent(ResponseInterface $response, $content)
    {
        $body = new Stream('php://memory', 'rw+');
        $body->write($content);
        return $response->withBody($body);
    }
}