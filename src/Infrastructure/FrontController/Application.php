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
use Slick\WebStack\FrontControllerSlickModule;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;

/**
 * Application
 *
 * @package Slick\WebStack\Infrastructure\FrontController
 */
final class Application
{
    private DependencyContainerFactory $containerFactory;

    /** @var array<SlickModuleInterface>  */
    private array $modules;

    private MiddlewareList $middlewareList;

    public function __construct(
        private readonly ServerRequestInterface $request,
        private readonly string $rootPath
    ) {
        $this->middlewareList = new MiddlewareList();
        $this->containerFactory = DependencyContainerFactory::instance();
        $this->modules = [new FrontControllerSlickModule()];
    }

    /**
     * Retrieves the root path of the application.
     *
     * @return string The root path.
     */
    public function rootPath(): string
    {
        return $this->rootPath;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function run(): ResponseInterface
    {

        $this->loadServices();
        $this->loadMiddlewares();

        return $this->startServerRequestHandler()
            ->process($this->request);
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
            $this->updateMiddlewareList($module);
        }
    }

    private function loadServices(): void
    {
        $services = [];
        foreach ($this->modules as $module) {
            $services = array_merge($services, $module->services());
        }

        $this->containerFactory->loadApplicationServices($this->rootPath(), $services);

        $container = $this->containerFactory->container();
        $container->register(ServerRequestInterface::class, $this->request);
        $container->register('http.request', $this->request);
    }

    /**
     * Updates the middleware list for a given module.
     *
     * @param SlickModuleInterface $module The module to update the middleware list for.
     *
     * @return void
     */
    private function updateMiddlewareList(SlickModuleInterface $module): void
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

        if (is_string($middlewareHandler)) {
            return $container->get($middlewareHandler);
        }

        if (is_callable($middlewareHandler)) {
            return new CallableMiddleware($middlewareHandler);
        }

        return $middlewareHandler;
    }
}
