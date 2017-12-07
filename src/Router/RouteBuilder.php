<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Router;

use Aura\Router\Map;
use Aura\Router\RouterContainer;
use Slick\WebStack\Exception\RoutesFileNotFoundException;
use Slick\WebStack\Exception\RoutesFileParserException;
use Slick\WebStack\Router\Builder\FactoryInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * RouteBuilder
 *
 * @package Slick\WebStack\Router
 */
class RouteBuilder implements RouteBuilderInterface
{

    /**
     * @var string
     */
    private $routesFile;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var FactoryInterface
     */
    private $routeFactory;

    /**
     * @var array
     */
    private $parsedData;

    /**
     * Creates a route builder
     *
     * @param string           $routesFile
     * @param Parser           $parser
     * @param FactoryInterface $routeFactory
     */
    public function __construct(
        $routesFile,
        Parser $parser,
        FactoryInterface $routeFactory
    ) {
        $this->routesFile = $routesFile;
        $this->parser = $parser;
        $this->routeFactory = $routeFactory;
    }

    /**
     * Map builder handler
     *
     * @see http://auraphp.com/packages/3.x/Router/custom-maps.html#1-4-5
     *
     * @param Map $map
     *
     * @return RouteBuilder|RouteBuilderInterface
     */
    public function build(Map $map)
    {
        $this->setDefaults($map);
        $this->addRoutes($map);
        return $this;
    }

    /**
     * Registers the callback for map creations
     *
     * @param RouterContainer $container
     * @return self|RouteBuilderInterface
     */
    public function register(RouterContainer $container)
    {
        $container->setMapBuilder([$this, 'build']);
        return $this;
    }

    /**
     * Get YML parsed data array
     *
     * @return array
     */
    private function getParsedData()
    {
        if (is_array($this->parsedData)) {
            return $this->parsedData;
        }

        $content = $this->getYmlFileContent();
        try {
            $parsedData = $this->parser->parse($content);
        } catch (ParseException $caught) {
            throw new RoutesFileParserException(
                $caught->getMessage(),
                0,
                $caught
            );
        }
        $this->parsedData = $parsedData;
        return $parsedData;
    }

    /**
     * Get contents form YML file
     *
     * @return string
     */
    private function getYmlFileContent()
    {
        if (!is_file($this->routesFile)) {
            throw new RoutesFileNotFoundException(
                "The routes file is not found on your system."
            );
        }

        return file_get_contents($this->routesFile);
    }

    /**
     * Set map defaults
     *
     * @param Map $map
     */
    private function setDefaults(Map $map)
    {
        $defaults = ['tokens', 'defaults', 'host', 'accepts'];
        foreach ($this->getParsedData() as $name => $value) {
            if (in_array($name, $defaults)) {
                $map->$name($value);
            }
        }
    }

    /**
     * Add routes to the provided route map
     *
     * @param Map $map
     */
    private function addRoutes(Map $map)
    {
        $data = $this->getParsedData();
        $routes = (array_key_exists('routes', $data))
            ? $data['routes']
            : [];
        foreach ($routes as $name => $definition) {
            $this->routeFactory->parse($name, $definition, $map);
        }
    }
}
