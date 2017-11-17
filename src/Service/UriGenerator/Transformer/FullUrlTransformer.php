<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service\UriGenerator\Transformer;

use Psr\Http\Message\UriInterface;
use Slick\WebStack\Service\UriGenerator\LocationTransformerInterface;

/**
 * Full URL Transformer
 *
 * @package Slick\WebStack\Service\UriGenerator\Transformer
 */
class FullUrlTransformer extends AbstractLocationTransformer implements LocationTransformerInterface
{

    /**
     * Tries to transform the provided location data into a server URI
     *
     * @param string $location Location name, path or identifier
     * @param array $options Filter options
     *
     * @return UriInterface|null
     */
    public function transform($location, array $options = [])
    {
        $regexp = '/((https?\:|\/\/).*)/i';
        if (preg_match($regexp, $location)) {
            return new CustomUri($location);
        }
        return null;
    }
}
