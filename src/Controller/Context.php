<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Controller;

use Interop\Container\ContainerInterface as InteropContainer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Di\ContainerInjectionInterface;
use Slick\WebStack\Service\UriGeneratorInterface;

/**
 * Context
 *
 * @package Slick\WebStack\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Context implements
    ControllerContextInterface,
    ContainerInjectionInterface
{
    /**
     * @var ResponseInterface
     */
    private $response;

    /**
     * @var ServerRequestInterface
     */
    private $request;
    /**
     * @var UriGeneratorInterface
     */
    private $uriGenerator;

    /**
     * Creates a controller context
     *
     * @param UriGeneratorInterface $uriGenerator
     */
    public function __construct(UriGeneratorInterface $uriGenerator)
    {
        $this->uriGenerator = $uriGenerator;
    }

    /**
     * Instantiates a new instance of this class.
     *
     * This is a factory method that returns a new instance of this class. The
     * factory should pass any needed dependencies into the constructor of this
     * class, but not the container itself. Every call to this method must return
     * a new instance of this class; that is, it may not implement a singleton.
     *
     * @param InteropContainer $container
     *   The service container this instance should use.
     *
     * @return Context
     */
    public static function create(InteropContainer $container)
    {
        return new Context($container->get(UriGeneratorInterface::class));
    }

    /**
     * Registers the HTTP request and response to this context
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     *
     * @return Context|ControllerContextInterface
     */
    public function register(
        ServerRequestInterface $request,
        ResponseInterface $response
    )
    {
        $this->request = $request;
        $this->setResponse($response);
        return $this;
    }

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
    public function getPost($name = null, $default = null)
    {
        $parameters = $this->request->getServerParams();
        return $this->getDataFrom($parameters, $name, $default);
    }

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
    public function getQuery($name = null, $default = null)
    {
        $parameters = $this->request->getQueryParams();
        return $this->getDataFrom($parameters, $name, $default);
    }

    /**
     * Sets a redirection header in the HTTP response
     *
     * @param string $location Location name, path or identifier
     * @param array  $options  Filter options
     *
     * @return void
     */
    public function redirect($location, array $options = [])
    {
        $response = $this->response
            ->withStatus(302)
            ->withHeader(
                'location',
                $this->uriGenerator->generate($location, $options)
            )
        ;
        $this->setResponse($response);
    }

    /**
     * Disables response rendering
     *
     * @return Context|ControllerContextInterface
     */
    public function disableRendering()
    {
        $this->request = $this->request->withAttribute('rendering', false);
        return $this;
    }

    /**
     * Sets the view that will be rendered
     *
     * @param string $viewPath
     *
     * @return Context|ControllerContextInterface
     */
    public function setView($viewPath)
    {
        $this->request = $this->request
            ->withAttribute('view', $viewPath);
        return $this;
    }

    /**
     * Sets a new response
     *
     * @param ResponseInterface $response
     *
     * @return Context|ControllerContextInterface
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Get current HTTP response
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get current HTTP request
     *
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Gets the parameters with provided name from parameters
     *
     * @param array       $parameters
     * @param string|null $name
     * @param mixed       $default
     *
     * @return array|string|mixed
     */
    private function getDataFrom(
        array $parameters,
        $name = null,
        $default = null
    ) {
        if ($name === null) {
            return $parameters;
        }

        $value = array_key_exists($name, $parameters)
            ? $parameters[$name]
            : $default;

        return $value;
    }

}