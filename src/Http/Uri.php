<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Http;

use Psr\Http\Message\UriInterface;
use Slick\Http\Uri as BaseUri;

/**
 * HTTP Uri
 *
 * @package Slick\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Uri extends BaseUri
{

    /**
     * Retrieve the path segment of the URI.
     *
     * This method always return a string; if no path is present it will return
     * a '/' string to properly be routed by Aura Router. This differs from
     * the PSR-7 recommended implementation with this method should return an
     * empty string if the path is not present.
     *
     * @return string The path segment of the URI.
     */
    public function getPath()
    {
        $path = parent::getPath();
        return str_replace('//', '/', "/$path");
    }

    /**
     * Factory method to create an URI object from an existing
     * PSR-7 UriInterface.
     *
     * @param UriInterface $uri
     *
     * @return Uri
     */
    public static function create(UriInterface $uri)
    {
        return new static((string) $uri);
    }
}