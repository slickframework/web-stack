<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Controller;

use Aura\Router\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller Context
 *
 * @package Slick\WebStack\Controller
 */
class Context implements ControllerContextInterface
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var Route
     */
    private $route;

    /**
     * Creates a controller context
     *
     * @param ServerRequestInterface $request
     * @param Route                  $route
     */
    public function __construct(ServerRequestInterface $request, Route $route)
    {
        $this->request = $request;
        $this->route = $route;
    }

    /**
     * Gets the post parameter that was submitted with provided name
     *
     * If its not submitted the default value will be returned.
     * If no arguments are passed the full server parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param null|string $name
     * @param mixed $default
     *
     * @return array|string
     */
    public function postParam($name = null, $default = null)
    {
        // TODO: Implement postParam() method.
    }

    /**
     * Gets the URL query parameter with provided name
     *
     * If its not submitted the default value will be returned.
     * If no arguments are passed the full URL query parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param null|string $name
     * @param mixed $default
     *
     * @return array|string
     */
    public function queryParam($name = null, $default = null)
    {
        // TODO: Implement queryParam() method.
    }

    /**
     * Gets the route parameter with provided name
     *
     * If its not submitted the default value will be returned.
     * If no arguments are passed the full URL query parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param null|string $name
     * @param mixed $default
     *
     * @return array|string
     */
    public function routeParam($name = null, $default = null)
    {
        // TODO: Implement routeParam() method.
    }

    /**
     * Sets a redirection header in the HTTP response
     *
     * @param string $location Location name, path or identifier
     * @param array $options Filter options
     *
     * @return void
     */
    public function redirect($location, array $options = [])
    {
        // TODO: Implement redirect() method.
    }

    /**
     * Disables response rendering
     *
     * @return self|ControllerContextInterface
     */
    public function disableRendering()
    {
        // TODO: Implement disableRendering() method.
    }

    /**
     * Sets the view template to use by render process
     *
     * @param string $template
     *
     * @return self|ControllerContextInterface
     */
    public function useTemplate($template)
    {
        // TODO: Implement useTemplate() method.
    }

    /**
     * Sets a new response
     *
     * @param ResponseInterface $response
     *
     * @return self|ControllerContextInterface
     */
    public function setResponse(ResponseInterface $response)
    {
        // TODO: Implement setResponse() method.
    }

    /**
     * Sets a new or updated server request
     *
     * @param ServerRequestInterface $request
     *
     * @return self|ControllerContextInterface
     */
    public function changeRequest(ServerRequestInterface $request)
    {
        // TODO: Implement changeRequest() method.
    }

    /**
     * Get current HTTP response
     *
     * @return ResponseInterface
     */
    public function response()
    {
        // TODO: Implement response() method.
    }

    /**
     * Get current HTTP request
     *
     * @return ServerRequestInterface
     */
    public function request()
    {
        // TODO: Implement request() method.
    }

    /**
     * True when it handles the response
     *
     * @return boolean
     */
    public function handlesResponse()
    {
        // TODO: Implement handlesResponse() method.
    }
}