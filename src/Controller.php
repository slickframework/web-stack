<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\Http\PhpEnvironment\Response;

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
}