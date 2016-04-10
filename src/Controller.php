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
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\PhpEnvironment\Response;
use Slick\Mvc\Controller\UrlUtils;

/**
 * Controller
 *
 * @package Slick\Mvc
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
abstract class Controller implements ControllerInterface
{

    /**
     * @var ServerRequestInterface|Request
     */
    protected $request;

    /**
     * @var ResponseInterface|Response
     */
    protected $response;

    /**
     * For URL parsing
     */
    use UrlUtils;

    /**
     * Registers the current HTTP request and response
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return Controller|$this|self|ControllerInterface
     */
    public function register(
        ServerRequestInterface $request, ResponseInterface $response
    ) {
        $this->request = $request;
        $this->response = $response;
        return $this;
    }

    /**
     * Gets updated HTTP response
     *
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Gets updated HTTP request
     *
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Sets a value to be used by render
     *
     * The key argument can be an associative array with values to be set
     * or a string naming the passed value. If an array is given then the
     * value will be ignored.
     *
     * Those values must be set in the request attributes so they can be used
     * latter by any other middle ware in the stack.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return Controller|$this|self|ControllerInterface
     */
    public function set($key, $value = null)
    {
        if (is_string($key)) {
            return $this->registerVar($key, $value);
        }

        foreach ($key as $name => $value) {
            $this->registerVar($name, $value);
        }
        return $this;
    }

    /**
     * Enables or disables rendering
     *
     * @param bool $disable
     * @return ControllerInterface|self|$this
     */
    public function disableRendering($disable = true)
    {
        $this->request = $this->request->withAttribute('render', !$disable);
        return $this;
    }

    /**
     * Changes the current rendering template
     *
     * @param string $template
     * @return ControllerInterface|self|$this
     */
    public function setView($template)
    {
        $this->request = $this->request->withAttribute('template', $template);
        return $this;
    }

    /**
     * Redirects the flow to another route/path
     *
     * @param string $path the route or path to redirect to
     *
     * @return Controller|self|$this
     */
    public function redirect($path)
    {
        $args = func_get_args();
        $url = call_user_func_array([$this, 'getUrl'], $args);
        $this->response = $this->createRedirectResponse($url);
        $this->disableRendering();
        return $this;
    }

    /**
     * Get the routed request attributes
     *
     * @param null|string $name
     * @param mixed       $default
     *
     * @return mixed
     */
    public function getRouteAttributes($name = null, $default = null)
    {
        /** @var Route $route */
        $route = $this->request->getAttribute('route', false);
        $attributes = $route
            ? $route->attributes
            : [];
        
        if (null == $name) {
            return $attributes;
        }
        
        return array_key_exists($name, $attributes)
            ? $attributes[$name]
            : $default;
    }

    /**
     * Register a variable value
     *
     * @param string $key
     * @param mixed $value
     *
     * @return Controller|$this|self
     */
    protected function registerVar($key, $value)
    {
        $attrName = ControllerInterface::REQUEST_ATTR_VIEW_DATA;
        $attributes = $this->request->getAttributes();
        $attributes[$attrName][$key] = $value;
        $this->request = $this->request
            ->withAttribute($attrName, $attributes[$attrName]);
        return $this;
    }

    /**
     * Creates a redirect response for provided path
     * 
     * @param string $path
     * 
     * @return Response
     */
    protected function createRedirectResponse($path)
    {
        $response = $this->response->withStatus(302)
            ->withHeader('Location', $path);
        return $response;
    }
}