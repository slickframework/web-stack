<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service\UriGenerator\Transformer;

use Psr\Http\Message\UriInterface;
use Slick\Http\Uri;
use Slick\WebStack\Service\UriGenerator\LocationTransformerInterface;

/**
 * Base Path Transformer
 *
 * @package Slick\WebStack\Service\UriGenerator\Transformer
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class BasePathTransformer extends AbstractLocationTransformer implements
    LocationTransformerInterface
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
        $this->options = array_merge($this->options, $options);
        if (!$this->request) {
            return null;
        }

        $path = str_replace('//', '/', $this->getBasePath()."/$location");
        $uri = (new Uri())->withPath($path);
        $uri = $this->updateOptions($uri);
        return $uri;
    }

    /**
     * Get the request base path
     *
     * @return string
     */
    private function getBasePath()
    {
        $name = $this->request->getServerParams()['SCRIPT_NAME'];
        return preg_replace('/(\w+\.php)/i', '', $name);
    }

}