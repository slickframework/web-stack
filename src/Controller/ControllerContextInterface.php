<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Controller Context Interface
 *
 * @package Slick\WebStack\Controller
 */
interface ControllerContextInterface
{

    /**
     * Gets the post parameter that was submitted with provided name
     *
     * If its not submitted the default value will be returned.
     * If no arguments are passed the full server parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param null|string $name
     * @param mixed       $default
     *
     * @return array|string
     */
    public function postParam($name = null, $default = null);

    /**
     * Gets the URL query parameter with provided name
     *
     * If its not submitted the default value will be returned.
     * If no arguments are passed the full URL query parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param null|string $name
     * @param mixed       $default
     *
     * @return array|string
     */
    public function queryParam($name = null, $default = null);

    /**
     * Gets the route parameter with provided name
     *
     * If its not submitted the default value will be returned.
     * If no arguments are passed the full URL query parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param null|string $name
     * @param mixed       $default
     *
     * @return array|string
     */
    public function routeParam($name = null, $default = null);

    /**
     * Sets a redirection header in the HTTP response
     *
     * @param string $location Location name, path or identifier
     * @param array  $options  Filter options
     *
     * @return void
     */
    public function redirect($location, array $options = []);

    /**
     * Disables response rendering
     *
     * @return self|ControllerContextInterface
     */
    public function disableRendering();

    /**
     * Sets the view template to use by render process
     *
     * @param string $template
     *
     * @return self|ControllerContextInterface
     */
    public function useTemplate($template);

    /**
     * Sets a new response
     *
     * @param ResponseInterface $response
     *
     * @return self|ControllerContextInterface
     */
    public function setResponse(ResponseInterface $response);

    /**
     * Sets a new or updated server request
     *
     * @param ServerRequestInterface $request
     *
     * @return self|ControllerContextInterface
     */
    public function changeRequest(ServerRequestInterface $request);

    /**
     * Get current HTTP response
     *
     * @return ResponseInterface
     */
    public function response();

    /**
     * Get current HTTP request
     *
     * @return ServerRequestInterface
     */
    public function request();

    /**
     * True when it handles the response
     *
     * @return boolean
     */
    public function handlesResponse();

    /**
     * Checks the request method
     *
     * @param string $methodName
     *
     * @return boolean
     */
    public function requestIs($methodName);
}
