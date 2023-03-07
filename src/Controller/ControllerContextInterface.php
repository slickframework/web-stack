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
     * If it's not submitted the default value will be returned.
     * If no arguments are passed the full server parameters from request will
     * be returned. In this case the default value is ignored.
     *
     * @param string|null $name
     * @param mixed|null $default
     *
     * @return array|string
     */
    public function postParam(string $name = null, mixed $default = null): mixed;

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
     * @return array|string
     */
    public function queryParam(string $name = null, mixed $default = null): mixed;

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
     * @return array|string
     */
    public function routeParam(string $name = null, mixed $default = null): mixed;

    /**
     * Sets a redirection header in the HTTP response
     *
     * @param string $location Location name, path or identifier
     * @param array|null $options Filter options
     *
     * @return ResponseInterface
     */
    public function redirect(string $location, ?array $options = []): ResponseInterface;

    /**
     * Disables response rendering
     *
     * @return self
     * @deprecated Controller shouldn't control what is handled by a further middleware in stack
     */
    public function disableRendering(): ControllerContextInterface;

    /**
     * Sets the view template to use by render process
     *
     * @param string $template
     *
     * @return self
     * @deprecated Controller shouldn't control what is handled by a further middleware in stack
     */
    public function useTemplate(string $template): self;

    /**
     * Sets a new response
     *
     * @param ResponseInterface $response
     *
     * @return self
     * @deprecated Use the controller handler method to return a PSR-7 response message
     */
    public function setResponse(ResponseInterface $response): self;

    /**
     * Sets a new or updated server request
     *
     * @param ServerRequestInterface $request
     *
     * @return self
     */
    public function changeRequest(ServerRequestInterface $request): self;

    /**
     * Get current HTTP response
     *
     * @return ResponseInterface
     * @deprecated Use the controller handler method to return a PSR-7 response message
     */
    public function response(): ?ResponseInterface;

    /**
     * Get current HTTP request
     *
     * @return ServerRequestInterface
     */
    public function request(): ServerRequestInterface;

    /**
     * True when it handles the response
     *
     * @return boolean
     * @deprecated Use the controller handler method to return a PSR-7 response message
     */
    public function handlesResponse(): bool;

    /**
     * Checks the request method
     *
     * @param string $methodName
     *
     * @return boolean
     */
    public function requestIs(string $methodName): bool;
}
