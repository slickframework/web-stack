<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Router;

use Aura\Router\Map;
use Aura\Router\Route;
use Aura\Router\RouterContainer;
use Prophecy\Argument;
use Slick\WebStack\Exception\RoutesFileNotFoundException;
use Slick\WebStack\Exception\RoutesFileParserException;
use Slick\WebStack\Router\Builder\FactoryInterface;
use Slick\WebStack\Router\Parsers\SymfonyYmlParser;
use Slick\WebStack\Router\RouteBuilder;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Router\RouteBuilderInterface;
use Slick\WebStack\Router\RoutesParser;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

/**
 * RouteBuilderSpec specs
 *
 * @package spec\Slick\WebStack\Router
 */
class RouteBuilderSpec extends ObjectBehavior
{
    function let(RoutesParser $parser, FactoryInterface $routeFactory, Route $route)
    {
        $routeFactory->parse(
            Argument::type('string'),
            Argument::any(),
            Argument::type(Map::class),
        )->willReturn($route);
        $file = __DIR__ . '/routes.yml';
        $this->beConstructedWith($file, $parser, $routeFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RouteBuilder::class);
    }

    function its_a_route_builder()
    {
        $this->shouldBeAnInstanceOf(RouteBuilderInterface::class);
    }

    function it_registers_itself_to_a_router_container(
        RouterContainer $container
    ) {
        $this->register($container)->shouldBe($this->getWrappedObject());
        $container->setMapBuilder([$this->getWrappedObject(), 'build'])
            ->shouldHaveBeenCalled();
    }

    function it_can_parse_yml_files(
        RoutesParser $parser,
        FactoryInterface $routeFactory,
        Map $map
    ) {
        $file = __DIR__ . '/routes.yml';
        $this->beConstructedWith($file, $parser, $routeFactory);
        $defaults = [];
        $parser->parse(file_get_contents($file))->willReturn($defaults);
        $this->build($map);
        $parser->parse(file_get_contents($file))->shouldHaveBeenCalled();
    }

    function it_can_set_router_defaults(
        RoutesParser $parser,
        FactoryInterface $routeFactory
    ) {
        $map = new Map(new Route());
        $file = __DIR__ . '/routes.yml';
        $defaults = [
            'defaults' => [
                'namespace' => 'Controller',
                'action' => 'index',
                'controller' => 'pages'
            ]
        ];
        $parser->parse(file_get_contents($file))->willReturn($defaults);
        $this->beConstructedWith($file, $parser, $routeFactory);
        $this->build($map);
    }

    function it_can_parser_routes_from_yml(
        RoutesParser $parser,
        FactoryInterface $routeFactory,
        Map $map
    ) {
        $file = __DIR__ . '/routes.yml';
        $routes = [
            'routes' => [
                'home' => [
                    'allows' => ['GET', 'POST'],
                    'path' => '/',
                    'defaults' => [
                        'action' => 'home'
                    ]
                ]
            ]
        ];
        $parser->parse(file_get_contents($file))
            ->willReturn($routes);
        $this->beConstructedWith($file, $parser, $routeFactory);
        $this->build($map);
        $routeFactory->parse('home', $routes['routes']['home'], $map)->shouldHaveBeenCalled();
    }

    function it_throws_exception_when_routes_file_is_not_found(
        RoutesParser $parser,
        FactoryInterface $routeFactory,
        Map $map
    ) {
        $file = 'some/where/over/the/disk/but/not/found.yml';
        $this->beConstructedWith($file, $parser, $routeFactory);
        $this->shouldThrow(RoutesFileNotFoundException::class)
            ->during('build',  [$map]);
    }

    function it_throws_exception_for_yml_parsing_errors(
        RoutesParser $parser,
        FactoryInterface $routeFactory,
        Map $map
    ) {
        $file = __DIR__ . '/routes.yml';
        $parser->parse(file_get_contents($file))
            ->willThrow(new ParseException('test'));
        $this->beConstructedWith($file, $parser, $routeFactory);
        $this->shouldThrow(RoutesFileParserException::class)
            ->during('build',  [$map]);
    }

    function it_reads_multiple_definition_files(FactoryInterface $routeFactory, Map $map)
    {
        $file = __DIR__ . '/routes.yml';
        $this->beConstructedWith($file, new SymfonyYmlParser(new Parser()), $routeFactory);
        $this->build($map);
        $routeFactory->parse('articles:article.read', Argument::type('array'), $map)->shouldHaveBeenCalled();
    }
}
