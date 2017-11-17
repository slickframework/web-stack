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
use Slick\Http\Message\Response;
use Slick\WebStack\Service\UriGeneratorInterface;

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
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var Route
     */
    private $route;

    /**
     * @var bool
     */
    private $handleResponse = false;

    /**
     * @var UriGeneratorInterface
     */
    private $uriGenerator;

    /**
     * Creates a controller context
     *
     * @param ServerRequestInterface $request
     * @param Route $route
     * @param UriGeneratorInterface $uriGenerator
     */
    public function __construct(ServerRequestInterface $request, Route $route, UriGeneratorInterface $uriGenerator)
    {
        $this->request = $request;
        $this->route = $route;
        $this->uriGenerator = $uriGenerator;
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
        return $this->getData(
            $this->request->getParsedBody(),
            $name,
            $default
        );
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
        return $this->getData(
            $this->request->getQueryParams(),
            $name,
            $default
        );
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
        return $this->getData($this->route->attributes, $name, $default);
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
        $this->disableRendering();
        $location = (string) $this->uriGenerator->generate($location, $options);
        $response = new Response(302, '', ['Location' => $location]);
        $this->setResponse($response);
    }

    /**
     * Disables response rendering
     *
     * @return self|ControllerContextInterface
     */
    public function disableRendering()
    {
        $this->handleResponse = true;
        return $this;
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
        $this->request = $this->request->withAttribute('template', $template);
        return $this;
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
        $this->response = $response;
        return $this;
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
        $this->request = $request;
        return $this;
    }

    /**
     * Get current HTTP response
     *
     * @return ResponseInterface
     */
    public function response()
    {
        return $this->response;
    }

    /**
     * Get current HTTP request
     *
     * @return ServerRequestInterface
     */
    public function request()
    {
        return $this->request;
    }

    /**
     * True when it handles the response
     *
     * @return boolean
     */
    public function handlesResponse()
    {
        return $this->handleResponse;
    }

    /**
     * Checks the request method
     *
     * @param string $methodName
     *
     * @return boolean
     */
    public function requestIs($methodName)
    {
        $method = $this->request->getMethod();
        return $method === strtoupper($methodName);
    }

    /**
     * Gets the value(s) from provided data
     *
     * @param mixed       $data
     * @param null|string $name
     * @param null|string $default
     *
     * @return mixed
     */
    private function getData($data, $name = null, $default = null)
    {
        if ($name == null) {
            return $data;
        }

        $value = $default;
        if (is_array($data) && array_key_exists($name, $data)) {
            $value = $data[$name];
        }

        return $value;
    }
}
