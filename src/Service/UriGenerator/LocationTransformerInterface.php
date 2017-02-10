<?php

/**
 * This file is part of slick/mvc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service\UriGenerator;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Location Transformer Interface
 *
 * @package Slick\WebStack\Service\UriGenerator
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
interface LocationTransformerInterface
{

    /**
     * Tries to transform the provided location data into a server URI
     *
     * @param string $location Location name, path or identifier
     * @param array  $options  Filter options
     *
     * @return UriInterface|null
     */
    public function transform($location, array $options = []);

    /**
     * Set the context HTTP request
     *
     * @param ServerRequestInterface $request
     *
     * @return self|LocationTransformerInterface
     */
    public function setRequest(ServerRequestInterface $request);
}
