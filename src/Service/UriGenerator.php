<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slick\WebStack\Service\UriGenerator\LocationTransformerInterface;
use Slick\WebStack\Service\UriGenerator\UriDecoratorInterface;

/**
 * UriGenerator
 *
 * @package Slick\WebStack\Service
 */
class UriGenerator implements UriGeneratorInterface
{

    /**
     * @var LocationTransformerInterface[]
     */
    private $transformers = [];

    /**
     * @var UriDecoratorInterface[]
     */
    private $decorators = [];

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * Generates an URL location from provided data
     *
     * @param string $location Location name, path or identifier
     * @param array $options Filter options
     *
     * @return UriInterface|string|null
     */
    public function generate($location, array $options = [])
    {
        $uri = $this->fetchUrl($location, $options);
        $this->decorate($uri);
        return $uri;
    }

    /**
     * Adds a location transformer to the transformers stack
     *
     * @param LocationTransformerInterface $transformer
     *
     * @return self|UriGeneratorInterface
     */
    public function addTransformer(LocationTransformerInterface $transformer)
    {
        array_push($this->transformers, $transformer);
        return $this;
    }

    /**
     * Adds an URI decorator to the decorators list
     *
     * @param UriDecoratorInterface $decorator
     * @return mixed
     */
    public function addDecorator(UriDecoratorInterface $decorator)
    {
        array_push($this->decorators, $decorator);
        return $this;
    }

    /**
     * Set the context HTTP request
     *
     * @param ServerRequestInterface $request
     *
     * @return self|UriGeneratorInterface
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Fetch the URI form the list of transformers
     *
     * The URI is the result of the first transformer that returns a
     * UriInterface object.
     *
     * @param string $location Location name, path or identifier
     * @param array $options   Filter options
     *
     * @return null|UriInterface
     */
    private function fetchUrl($location, array $options = [])
    {
        $uri = null;
        foreach ($this->transformers as $transformer) {
            $this->setContext($transformer);
            $object = $transformer->transform($location, $options);
            if ($object instanceof UriInterface) {
                $uri = $object;
                break;
            }
        }
        return $uri;
    }

    /**
     * Applies decorators to the provided URI
     *
     * @param UriInterface|null $uri
     */
    private function decorate(UriInterface $uri = null)
    {
        foreach ($this->decorators as $decorator) {
            $decorator->decorate($uri);
        }
    }

    /**
     * Sets the context HTTP request to provided transformer
     *
     * @param LocationTransformerInterface $transformer
     */
    private function setContext(LocationTransformerInterface $transformer)
    {
        if ($this->request) {
            $transformer->setRequest($this->request);
        }
    }
}
