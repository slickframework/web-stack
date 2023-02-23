<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Service\UriGenerator\Transformer;

use Psr\Http\Message\UriInterface;
use Slick\Http\Message\Exception\InvalidArgumentException;

/**
 * Custom URI
 *
 * @package Slick\WebStack\Service\UriGenerator\Transformer
 */
class CustomUri implements UriInterface
{
    private $defaultPorts = [
        'http' => 80,
        'https' => 443
    ];

    /**
     * @var string
     */
    private $scheme = '';

    /**
     * @var string
     */
    private $host = '';

    /**
     * @var string
     */
    private $port;

    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $pass;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $fragment;

    /**
     * Creates an empty HTTP URI
     * @param null $url
     */
    public function __construct($url = null)
    {
        if (null === $url) {
            return;
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException(
                "The URL entered is invalid. URI cannot be created."
            );
        }

        foreach (parse_url($url) as $property => $value) {
            $this->$property = $value;
        }
    }


    /**
     * Retrieve the scheme component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string The URI scheme.
     */
    public function getScheme()
    {
        return strtolower($this->scheme);
    }

    /**
     * Retrieve the authority component of the URI.
     *
     * @return string The URI authority, in "[user-info@]host[:port]" format.
     */
    public function getAuthority()
    {
        $authority = $this->getUserInfo() !== ''
            ? "{$this->getUserInfo()}@{$this->getHost()}"
            : "{$this->getHost()}";
        return $this->getPort() ? "{$authority}:{$this->getPort()}" : $authority;
    }

    /**
     * Retrieve the user information component of the URI.
     *
     * @return string The URI user information, in "username[:password]" format.
     */
    public function getUserInfo()
    {
        $userInfo = $this->user ? $this->user : '';
        return $this->pass ? "{$userInfo}:{$this->pass}" : $userInfo;
    }

    /**
     * Retrieve the host component of the URI.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string The URI host.
     */
    public function getHost()
    {
        return strtolower($this->host);
    }

    /**
     * Retrieve the port component of the URI.
     *
     * @return null|int The URI port.
     */
    public function getPort()
    {
        $default = array_key_exists($this->getScheme(), $this->defaultPorts)
            ? $this->defaultPorts[$this->getScheme()]
            : -1;

        if ($default === $this->port) {
            return null;
        }

        return (int) $this->port;
    }

    /**
     * Retrieve the path component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string The URI path.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retrieve the query string of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string The URI query string.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retrieve the fragment component of the URI.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string The URI fragment.
     */
    public function getFragment()
    {
        return $this->fragment ? $this->fragment : '';
    }

    /**
     * Return an instance with the specified scheme.
     *
     * @param string $scheme The scheme to use with the new instance.
     * @return static A new instance with the specified scheme.
     * @throws \InvalidArgumentException for invalid or unsupported schemes.
     */
    public function withScheme($scheme)
    {
        $this->validateCharacters($scheme);

        $uri = clone $this;
        $uri->scheme = $scheme;
        return $uri;
    }

    /**
     * Return an instance with the specified user information.
     *
     * @param string $user The user name to use for authority.
     * @param null|string $password The password associated with $user.
     * @return static A new instance with the specified user information.
     */
    public function withUserInfo($user, $password = null)
    {
        $uri = clone $this;
        $uri->user = $user;
        $uri->pass = $password;
        return $uri;
    }

    /**
     * Return an instance with the specified host.
     *
     * @param string $host The hostname to use with the new instance.
     * @return static A new instance with the specified host.
     * @throws \InvalidArgumentException for invalid hostnames.
     */
    public function withHost($host)
    {
        $this->validateCharacters($host);

        $uri = clone $this;
        $uri->host = $host;
        return $uri;
    }

    /**
     * Return an instance with the specified port.
     *
     * @param null|int $port The port to use with the new instance; a null value
     *     removes the port information.
     * @return static A new instance with the specified port.
     * @throws \InvalidArgumentException for invalid ports.
     */
    public function withPort($port)
    {
        $uri = clone $this;
        $uri->port = (int) $port;
        return $uri;
    }

    /**
     * Return an instance with the specified path.
     *
     * @param string $path The path to use with the new instance.
     * @return static A new instance with the specified path.
     * @throws \InvalidArgumentException for invalid paths.
     */
    public function withPath($path)
    {
        $uri = clone $this;
        $uri->path = $path;
        return $uri;
    }

    /**
     * Return an instance with the specified query string.
     *
     * An empty query string value is equivalent to removing the query string.
     *
     * @param string $query The query string to use with the new instance.
     * @return static A new instance with the specified query string.
     * @throws \InvalidArgumentException for invalid query strings.
     */
    public function withQuery($query)
    {
        $uri = clone $this;
        $uri->query = $query;
        return $uri;
    }

    /**
     * Return an instance with the specified URI fragment.
     *
     * An empty fragment value is equivalent to removing the fragment.
     *
     * @param string $fragment The fragment to use with the new instance.
     * @return static A new instance with the specified fragment.
     */
    public function withFragment($fragment)
    {
        $uri = clone $this;
        $uri->fragment = $fragment;
        return $uri;
    }

    /**
     * Return the string representation as a URI reference.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     * @return string
     */
    public function __toString()
    {
        $text  = $this->scheme ? "{$this->getScheme()}:" : '';
        $text .= $this->getAuthority() !== ''
            ? "//{$this->getAuthority()}"
            : '';
        $text .= '/'. ltrim($this->getPath(), '/');
        $text .= !is_null($this->query)  && strlen($this->query) > 0 ? "?{$this->query}" : '';
        $text .= !is_null($this->fragment) && strlen($this->fragment) > 0 ? "#{$this->fragment}" : '';
        return $text;
    }

    /**
     * Check the provided text for invalid characters
     *
     * @param string $text
     */
    private function validateCharacters($text)
    {
        $regex = '/^[a-z]{1}[a-z0-9\.\+\-]*/i';
        if ($text !== '' && ! preg_match($regex, $text)) {
            throw new InvalidArgumentException(
                "Invalid characters used in URI."
            );
        }
    }
}
