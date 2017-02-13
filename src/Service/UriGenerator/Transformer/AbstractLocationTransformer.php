<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service\UriGenerator\Transformer;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Slick\WebStack\Service\UriGenerator\LocationTransformerInterface;

/**
 * Abstract Location Transformer
 *
 * @package Slick\WebStack\Service\UriGenerator\Transformer
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
abstract class AbstractLocationTransformer implements
    LocationTransformerInterface
{

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var array
     */
    protected $options = [
        'query' => [],
        'reuseHostName' => false,
        'reuseParams' => false
    ];

    /**
     * Set the context HTTP request
     *
     * @param ServerRequestInterface $request
     *
     * @return BasePathTransformer|LocationTransformerInterface
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Tries to transform the provided location data into a server URI
     *
     * @param string $location Location name, path or identifier
     * @param array $options Filter options
     *
     * @return UriInterface|null
     */
    abstract public function transform($location, array $options = []);

    /**
     * Updates the URI according to existing options
     *
     * @param UriInterface $uri
     *
     * @return UriInterface
     */
    protected function updateOptions(UriInterface $uri)
    {
        $query = $this->options['query'];
        if ($this->options['reuseParams']) {
            $query = array_merge(
                $query,
                $this->request->getQueryParams()
            );
        }
        $queryString = http_build_query($query);
        $uri = $uri->withQuery($queryString);
        return $this->updateHostName($uri);
    }

    /**
     * Adds the scheme, host name and port to the provided URI
     *
     * @param UriInterface $uri
     *
     * @return UriInterface
     */
    private function updateHostName(UriInterface $uri)
    {
        if (!$this->options['reuseHostName']) {
            return $uri;
        }

        $params = $this->request->getServerParams();

        $hasPort = !in_array($params['SERVER_PORT'], ['80', '443']);
        if ($hasPort) {
            $uri = $uri->withPort($params['SERVER_PORT']);
        }

        $isSecure = array_key_exists('HTTPS', $params) &&
            $params['HTTPS'] != strtolower('off');
        $scheme =  $isSecure ? 'https' : 'http';

        /** @var UriInterface $uri */
        $uri = $uri->withHost($params['SERVER_NAME']);
        $uri = $uri->withScheme($scheme);

        return $uri;
    }
}
