<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Controller;

use Aura\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Service\UriGeneratorInterface;

/**
 * Context Creator
 *
 * @package Slick\WebStack\Controller
 */
class ContextCreator
{
    /**
     * @var UriGeneratorInterface
     */
    private $uriGenerator;

    /**
     * Creates a ContextCreator
     *
     * @param UriGeneratorInterface $uriGenerator
     */
    public function __construct(UriGeneratorInterface $uriGenerator)
    {
        $this->uriGenerator = $uriGenerator;
    }

    /**
     * Creates a container context for provided request and route
     *
     * @param ServerRequestInterface $request
     * @param Route                  $route
     *
     * @return ControllerContextInterface
     */
    public function create(ServerRequestInterface $request, Route $route)
    {
        $this->uriGenerator->setRequest($request);
        return new Context($request, $route, $this->uriGenerator);
    }
}
