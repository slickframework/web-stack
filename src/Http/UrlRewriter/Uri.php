<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Http\UrlRewriter;

use Psr\Http\Message\UriInterface;

/**
 * Uri
 *
 * @package Slick\WebStack\Http\UrlRewriter
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Uri extends \Slick\Http\Uri implements UriInterface
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
}