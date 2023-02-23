<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http\Server;

use Aura\Router\Route;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Controller\ContextCreator;
use Slick\WebStack\Controller\ControllerContextInterface;
use Slick\WebStack\Dispatcher\ControllerDispatchInflectorInterface;
use Slick\WebStack\Dispatcher\ControllerInvokerInterface;
use Slick\WebStack\Exception\BadHttpStackConfigurationException;
use Slick\WebStack\Exception\MissingResponseException;

/**
 * DispatcherMiddleware
 *
 * @package Slick\WebStack\Http\Server
 */
class DispatcherMiddleware implements MiddlewareInterface
{
    /**
     * DispatcherMiddleware constructor.
     * @param ControllerDispatchInflectorInterface $inflector
     * @param ControllerInvokerInterface $invoker
     * @param ContextCreator $contextCreator
     */
    public function __construct(
        private ControllerDispatchInflectorInterface $inflector,
        private ControllerInvokerInterface $invoker,
        private ContextCreator $contextCreator
    ) {
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws BadHttpStackConfigurationException When the need route is missing from the request
     * @throws MissingResponseException When context has disabled the rendering and has no response object to return
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->route($request);
        $controllerDispatch = $this->inflector->inflect($route);
        $context = $this->contextCreator->create($request, $route);

        $invoke = $this->invoker->invoke($context, $controllerDispatch);
        list($response, $data) = $invoke;


        if ($context->handlesResponse()) {
            return $this->responseFrom($context);
        }

        $request = $context->request()->withAttribute('viewData', $this->merge($request, $data));

        return $response instanceof ResponseInterface ? $response : $handler->handle($request);
    }

    /**
     * Get route attribute from request
     *
     * @param ServerRequestInterface $request
     *
     * @return Route
     *
     * @throws BadHttpStackConfigurationException When the need route is missing from the request
     */
    private function route(ServerRequestInterface $request)
    {
        $route = $request->getAttribute('route', false);
        if (! $route instanceof Route) {
            throw new BadHttpStackConfigurationException(
                "Dispatcher works with router middleware in order to process incoming requests. " .
                "Please check that the HTTP stack has the router middleware placed before the dispatcher. ".
                "Missing route in the request."
            );
        }
        return $route;
    }

    /**
     * Merges any existing view data in the request with the provided one
     *
     * @param ServerRequestInterface $request
     * @param array                  $data
     *
     * @return array
     */
    private function merge(ServerRequestInterface $request, array $data)
    {
        $existing = $request->getAttribute('viewData', []);
        return array_merge($existing, $data);
    }

    /**
     * Verify and return the response from the context
     *
     * @param ControllerContextInterface $context
     *
     * @return ResponseInterface
     *
     * @throws MissingResponseException When context has no response object to return
     */
    private function responseFrom(ControllerContextInterface $context)
    {
        $response = $context->response();
        if (!$response instanceof ResponseInterface) {
            throw new MissingResponseException(
                "Missing response object after handle the request. If you disabled rendering you need to ".
                "provide an HTTP response object to be returned to the client. Use Context::setResponse() ".
                "to provide a response to be returned."
            );
        }

        return $response;
    }
}
