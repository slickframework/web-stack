<?php

/**
 * This file is part of php-scaffold
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Http;

use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionException;
use Slick\WebStack\Domain\Security\Attribute\IsGranted;
use Slick\WebStack\Domain\Security\AuthorizationCheckerInterface;
use Slick\WebStack\Domain\Security\UserInterface;

/**
 * AuthorizationMiddleware
 *
 * @package Slick\WebStack\Infrastructure\Http
 */
final readonly class AuthorizationMiddleware implements MiddlewareInterface
{

    /**
     * Creates a AuthorizationMiddleware
     *
     * @template T of UserInterface
     * @param AuthorizationCheckerInterface<T> $authorizationChecker
     */
    public function __construct(private AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    /**
     * @inheritDoc
     * @throws ReflectionException|JsonException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $request->getAttribute('route');
        $response = $this->checkClassAuthorization($route, $request);

        if ($response instanceof ResponseInterface) {
            return $response;
        }

        return $handler->handle($request);
    }

    /**
     * Checks if the current user is authorized to access the class based on the route information
     *
     * @param array<string, string> $route The route information
     * @return ResponseInterface|null Returns a ResponseInterface object if the user is not authorized,
     *                                otherwise returns null
     * @throws ReflectionException|JsonException
     */
    private function checkClassAuthorization(?array $route, ServerRequestInterface $request): ?ResponseInterface
    {
        if (!is_array($route) || !array_key_exists('_controller', $route)) {
            return null;
        }

        /** @var class-string $className */
        $className = $route['_controller'];
        $reflectionClass = new ReflectionClass($className);
        $classAttributes = $reflectionClass->getAttributes(IsGranted::class);
        $reflectionMethod = $reflectionClass->getMethod($route['_action']);
        $methodAttributes = $reflectionMethod->getAttributes(IsGranted::class);

        return $this->checkAttributes(array_merge($classAttributes, $methodAttributes), $request);
    }

    /**
     * Check the attributes and return a ResponseInterface if any conditions are met.
     *
     * @param array<ReflectionAttribute<IsGranted>>|ReflectionAttribute<IsGranted>[] $attributes
     *
     * @return ResponseInterface|null The response if conditions are met, null otherwise.
     * @throws JsonException
     */
    private function checkAttributes(array $attributes, ServerRequestInterface $request): ?ResponseInterface
    {
        $last = null;
        foreach ($attributes as $attribute) {
            $grantInfo = $attribute->newInstance();
            if ($this->authorizationChecker->isGranted($grantInfo->attribute())) {
                return null;
            }
            $last = $grantInfo;
        }

        $response = $last ? $this->authorizationChecker->processEntryPoint($request) : null;
        $attributeResponse = $last ? $last->response() : null;

        return $response && $response->getStatusCode() < 400 ? $response : $attributeResponse;
    }
}
