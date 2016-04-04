<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc;

use Aura\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\Server\AbstractMiddleware;
use Slick\Http\Server\MiddlewareInterface;
use Slick\Http\Stream;
use Slick\Template\Template;

/**
 * Request Renderer
 *
 * @package Slick\Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Renderer extends AbstractMiddleware implements MiddlewareInterface
{

    /**
     * @var ServerRequestInterface
     */
    protected $request;

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
        $this->request = $request;
        $render = $this->request->getAttribute('render', true);
        if ($render) {
            $response = $this->render($response);
        }
        return $this->executeNext($request, $response);
    }

    /**
     * Adds a template path
     *
     * @param $path
     */
    public function addTemplatePath($path)
    {
        Template::addPath($path);
    }

    /**
     * Processes and renders the response
     *
     * @param ResponseInterface $response
     * @return ResponseInterface|static
     */
    protected function render(ResponseInterface $response)
    {
        $stream = new Stream('php://memory', 'rw+');
        $content = $this->getEngine()
            ->parse($this->getTemplate())
            ->process($this->getData())
        ;
        $stream->write($content);

        $response = $response->withBody($stream);
        return $response;
    }

    /**
     * Creates the engine for this rendering
     *
     * @return \Slick\Template\TemplateEngineInterface
     */
    protected function getEngine()
    {
        $template = new Template();
        return $template->initialize();
    }

    /**
     * Gets the template file for current request
     * 
     * @return string
     */
    protected function getTemplate()
    {
        $template = $this->request
            ->getAttribute('template', $this->getDefaultTemplate());
        return $template.'.twig';
    }

    /**
     * Gets data to be processed
     *
     * @return array
     */
    protected function getData()
    {
        $index = ControllerInterface::REQUEST_ATTR_VIEW_DATA;
        return $this->request->getAttribute($index, []);
    }

    /**
     * Returns the default template from current request route
     * 
     * @return string
     */
    protected function getDefaultTemplate()
    {
        /** @var Route $route */
        $route = $this->request->getAttribute('route');
        $values = $route->attributes;
        return "{$values['controller']}/{$values['action']}";
    }
}