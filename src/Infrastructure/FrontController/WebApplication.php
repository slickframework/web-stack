<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\FrontController;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slick\Http\Server\Middleware\CallableMiddleware;
use Slick\Http\Server\MiddlewareStack;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandlerInterface;
use Slick\ModuleApi\Infrastructure\FrontController\WebModuleInterface;
use Slick\WebStack\DispatcherModule;
use Slick\WebStack\FrontControllerModule;
use Slick\WebStack\Infrastructure\AbstractApplication;

/**
 * Application
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 */
final class WebApplication extends AbstractApplication
{

    private MiddlewareList $middlewareList;

    public function __construct(
        private readonly ServerRequestInterface $request,
        string $rootPath
    ) {

        $this->middlewareList = new MiddlewareList();
        parent::__construct($rootPath);
        $this->modules = [
            new FrontControllerModule(),
            new DispatcherModule(),
        ];
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): ResponseInterface
    {
        $container = $this->prepareContainer();
        $container->register(ServerRequestInterface::class, $this->request);
        $container->register('http.request', $this->request);

        $this->loadMiddlewares();

        return $this->startServerRequestHandler()
            ->process($this->request);
    }

    /**
     * Outputs the response.
     *
     * @param ResponseInterface $response The response to output.
     *
     * @return void
     */
    public function output(ResponseInterface $response): void
    {
        // output the response status
        http_response_code($response->getStatusCode());

        // Send response headers
        foreach ($response->getHeaders() as $name => $value) {
            $line = implode(', ', $value);
            header("$name: $line");
        }
        // Send response body
        print $response->getBody();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function startServerRequestHandler(): MiddlewareStack
    {
        $middlewareStack = new MiddlewareStack([]);
        foreach ($this->middlewareList as $middleware) {
            $middlewareStack->push($this->resolve($middleware));
        }
        return $middlewareStack;
    }

    private function loadMiddlewares(): void
    {
        foreach ($this->modules as $module) {
            if ($module instanceof WebModuleInterface) {
                $this->updateMiddlewareList($module);
            }
        }
    }

    /**
     * Updates the middleware list for a given module.
     *
     * @param WebModuleInterface $module The module to update the middleware list for.
     *
     * @return void
     */
    private function updateMiddlewareList(WebModuleInterface $module): void
    {
        foreach ($module->middlewareHandlers() as $middleware) {
            $this->middlewareList->add($middleware);
        }
    }

    /**
     * Resolves a MiddlewareHandlerInterface into a MiddlewareInterface.
     *
     * If the handler is a string, it will be treated as a service identifier and resolved from the container.
     * If the handler is a callable, it will be executed and the result will be returned as the middleware.
     * If the handler is neither a string nor a callable, it will be returned as is.
     *
     * @param MiddlewareHandlerInterface $middleware The middleware handler to resolve.
     *
     * @return MiddlewareInterface The resolved middleware.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function resolve(MiddlewareHandlerInterface $middleware): MiddlewareInterface
    {
        $container = $this->containerFactory->container();
        $middlewareHandler = $middleware->handler();

        if (is_callable($middlewareHandler)) {
            return new CallableMiddleware($middlewareHandler);
        }

        return is_string($middlewareHandler) ? $container->get($middlewareHandler) : $middlewareHandler;
    }
}
