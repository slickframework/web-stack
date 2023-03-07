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
final class Context implements ControllerContextInterface
{
    private ?ResponseInterface $response = null;
    private bool $handleResponse = false;


    /**
     * Creates a controller context
     *
     * @param ServerRequestInterface $request
     * @param Route $route
     * @param UriGeneratorInterface $uriGenerator
     */
    public function __construct(
        private ServerRequestInterface $request,
        private Route $route,
        private UriGeneratorInterface $uriGenerator
    ) {
    }

    /**
     * Gets the post parameter that was submitted with provided name
     *
     * If it's not submitted the default value will be returned.
     * If no arguments are passed the full server parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param string|null $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function postParam(string $name = null, mixed $default = null): mixed
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
     * If it's not submitted the default value will be returned.
     * If no arguments are passed the full URL query parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param string|null $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function queryParam(string $name = null, mixed $default = null): mixed
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
     * If it's not submitted the default value will be returned.
     * If no arguments are passed the full URL query parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param string|null $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function routeParam(string $name = null, mixed $default = null): mixed
    {
        return $this->getData($this->route->attributes, $name, $default);
    }

    /**
     * Sets a redirection header in the HTTP response
     *
     * @param string $location Location name, path or identifier
     * @param array|null $options Filter options
     *
     * @return ResponseInterface
     */
    public function redirect(string $location, ?array $options = []): ResponseInterface
    {
        $this->disableRendering();
        $location = (string) $this->uriGenerator->generate($location, $options);
        $response = new Response(302, '', ['Location' => $location]);
        $this->setResponse($response);
        return $response;
    }

    /**
     * Disables response rendering
     *
     * @return self
     */
    public function disableRendering(): self
    {
        $this->handleResponse = true;
        return $this;
    }

    /**
     * Sets the view template to use by render process
     *
     * @param string $template
     *
     * @return self
     */
    public function useTemplate(string $template): self
    {
        $this->request = $this->request->withAttribute('template', $template);
        return $this;
    }

    /**
     * Sets a new response
     *
     * @param ResponseInterface $response
     *
     * @return self
     */
    public function setResponse(ResponseInterface $response): self
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Sets a new or updated server request
     *
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public function changeRequest(ServerRequestInterface $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Get current HTTP response
     *
     * @return ResponseInterface|null
     */
    public function response(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Get current HTTP request
     *
     * @return ServerRequestInterface
     */
    public function request(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * True when it handles the response
     *
     * @return boolean
     */
    public function handlesResponse(): bool
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
    public function requestIs(string $methodName): bool
    {
        $method = $this->request->getMethod();
        return $method === strtoupper($methodName);
    }

    /**
     * Gets the value(s) from provided data
     *
     * @param mixed       $data
     * @param string|null $name
     * @param mixed $default
     *
     * @return mixed
     */
    private function getData(mixed $data, string $name = null, mixed $default = null): mixed
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
