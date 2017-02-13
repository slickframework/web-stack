<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service\UriGenerator;

use Psr\Http\Message\UriInterface;

/**
 * URI Decorator Interface
 *
 * @package Slick\WebStack\Service\UriGenerator
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
interface UriDecoratorInterface
{

    /**
     * Decorates provided URI
     *
     * @param null|UriInterface $uri The URI to decorate
     *
     * @return UriInterface
     */
    public function decorate(UriInterface $uri = null);
}
