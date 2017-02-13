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
 * URI Generator Interface
 *
 * @package Slick\WebStack\Service
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
interface UriGeneratorInterface
{

    /**
     * Generates an URL location from provided data
     *
     * @param string $location Location name, path or identifier
     * @param array  $options  Filter options
     *
     * @return UriInterface|null
     */
    public function generate($location, array $options = []);

    /**
     * Adds a location transformer to the transformers stack
     *
     * @param LocationTransformerInterface $transformer
     *
     * @return self|UriGeneratorInterface
     */
    public function addTransformer(LocationTransformerInterface $transformer);

    /**
     * Adds an URI decorator to the decorators list
     *
     * @param UriDecoratorInterface $decorator
     * @return self|UriGeneratorInterface
     */
    public function addDecorator(UriDecoratorInterface $decorator);

    /**
     * Set the context HTTP request
     *
     * @param ServerRequestInterface $request
     *
     * @return self|UriGeneratorInterface
     */
    public function setRequest(ServerRequestInterface $request);
}
