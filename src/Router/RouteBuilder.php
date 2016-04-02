<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Router;

use Aura\Router\Map;
use Slick\Mvc\Exception\RoutesFileNotFoundException;
use Slick\Mvc\Exception\RoutesFileParseException;
use Slick\Mvc\Router\Builder\RouteFactory;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * Route Builder
 *
 * @package Slick\Mvc\Router
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class RouteBuilder
{

    /**
     * @var Map
     */
    protected $map;

    /**
     * @var string
     */
    protected $routesFile;

    /**
     * @var Parser
     */
    protected $parser;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var RouteFactory
     */
    protected $routeFactory;

    /**
     * RouteBuilder constructor.
     *
     * @param string $routesFile
     */
    public function __construct($routesFile)
    {
        $this->routesFile = $routesFile;
    }

    /**
     * Map builder handler
     *
     * @see http://auraphp.com/packages/Aura.Router/custom-maps.html#2.5.3
     *
     * @param Map $map
     *
     * @return $this|self|RouteBuilder
     */
    public function build(Map $map)
    {
        $this->map = $map;
        $data = $this->getData();
        $this->setMapDefaults($data);
        $routes = (array_key_exists('routes', $data))
            ? $data['routes']
            : [];
        foreach ($routes as $name => $definition) {
            $this->getRouteFactory()
                ->parse($name, $definition, $this->map);
        }
        return $this;
    }

    /**
     * Gets the YML parser. Creates it if its null.
     *
     * @return Parser
     */
    public function getParser()
    {
        if (is_null($this->parser)) {
            $this->setParser(new Parser());
        }
        return $this->parser;
    }

    /**
     * Sets the YML parser
     *
     * @param Parser $parser
     *
     * @return $this|self|RouteBuilder
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * Returns parsed data from YML
     *
     * If no data is present the YML parser parses it and its values are
     * assigned to the data field.
     *
     * @return mixed
     */
    public function getData()
    {
        if (is_null($this->data)) {
            try {
                $this->data = $this->getParser()->parse($this->getYmlData());
            } catch (ParseException $exp) {
                throw new RoutesFileParseException(
                    "Fail to parse routes file: ".
                    $exp->getMessage(),
                    0,
                    $exp
                );
            }
        }
        return $this->data;
    }

    /**
     * Get route factory
     *
     * @return RouteFactory
     */
    public function getRouteFactory()
    {
        if (null == $this->routeFactory) {
            $this->setRouteFactory(new RouteFactory());
        }
        return $this->routeFactory;
    }

    /**
     * Set route factory
     *
     * @param RouteFactory $routeFactory
     *
     * @return RouteBuilder|self|$this
     */
    public function setRouteFactory(RouteFactory $routeFactory)
    {
        $this->routeFactory = $routeFactory;
        return $this;
    }


    /**
     * Gets routes files definition content
     *
     * @return string
     */
    protected function getYmlData()
    {
        if (!is_file($this->routesFile)) {
            throw new RoutesFileNotFoundException(
                "Routes file '{$this->routesFile}' is not found."
            );
        }
        return file_get_contents($this->routesFile);
    }

    /**
     * Gets the YML parser data and sets the map default defined
     *
     * @param array $data
     */
    protected function setMapDefaults(array $data)
    {
        $defaults = ['tokens', 'defaults', 'host', 'accepts'];
        foreach ($data as $name => $value) {
            if (in_array($name, $defaults)) {
                $this->map->$name($value);
            }
        }
    }
}