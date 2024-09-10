<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass;
use ReflectionException;
use Slick\Di\ContainerInterface;
use Slick\WebStack\Infrastructure\Exception\UnresolvedControllerArgument;

/**
 * DispatcherMiddleware
 *
 * @package Slick\WebStack\Infrastructure\Http
 */
final class DispatcherMiddleware implements MiddlewareInterface
{

    public function __construct(private ContainerInterface $container)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute('route');
        $controller = $this->container->make($route['_controller']);

        $result = $this->runAction($controller, $route['_action'], $route);

        if ($result instanceof ResponseInterface) {
            return $result;
        }

        return $handler->handle($request);
    }

    /**
     * @param mixed $controller
     * @param mixed $action
     * @param array<string, mixed> $route
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    private function runAction(mixed $controller, mixed $action, array $route): mixed
    {
        $reflection = new ReflectionClass($controller);
        $method = $reflection->getMethod($action);
        $parameters = $this->resolveParameters($route);
        $arguments = $method->getParameters();
        $resolvedArguments = [];
        foreach ($arguments as $argument) {
            $name = $argument->getName();
            if (array_key_exists($name, $parameters)) {
                $resolvedArguments[$name] = $parameters[$name];
                continue;
            }
            $type = (string) $argument->getType();

            if ($this->container->has($type)) {
                $resolvedArguments[$name] = $this->container->get($type);
                continue;
            }

            if ($argument->isOptional()) {
                continue;
            }

            throw new UnresolvedControllerArgument(
                "Cannot resolve argument '$name' for method '{$method->getName()}'"
            );
        }

        return $method->invokeArgs($controller, $resolvedArguments);
    }

    /**
     * Resolves the parameters from the given route array.
     *
     * @param array<string, mixed> $route The route array.
     * @return array<string, mixed> The resolved parameters.
     */
    private function resolveParameters(array $route): array
    {
        $parameters = [];
        foreach ($route as $key => $value) {
            if (str_starts_with($key, '_')) {
                continue;
            }
            $parameters[$key] = $value;
        }
        return $parameters;
    }
}
