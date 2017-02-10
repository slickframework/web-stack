<?php

namespace spec\Slick\WebStack\Http\Router;

use Aura\Router\Map;
use Aura\Router\Route;
use Aura\Router\RouterContainer;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Exception\RoutesFileNotFoundException;
use Slick\WebStack\Exception\RoutesFileParserException;
use Slick\WebStack\Http\Router\Builder\FactoryInterface;
use Slick\WebStack\Http\Router\RouteBuilder;
use Slick\WebStack\Http\Router\RouteBuilderInterface;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class RouteBuilderSpec extends ObjectBehavior
{
    function let(Parser $parser, FactoryInterface $routeFactory)
    {
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
    )
    {
        $this->register($container)->shouldBe($this->getWrappedObject());
        $container->setMapBuilder([$this->getWrappedObject(), 'build'])
            ->shouldHaveBeenCalled();
    }

    function it_can_parse_yml_files(
        Parser $parser,
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
        Parser $parser,
        FactoryInterface $routeFactory
    )
    {
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
        Parser $parser,
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
        Parser $parser,
        FactoryInterface $routeFactory,
        Map $map
    ) {
        $file = 'some/where/over/the/disk/but/not/found.yml';
        $this->beConstructedWith($file, $parser, $routeFactory);
        $this->shouldThrow(RoutesFileNotFoundException::class)
            ->during('build',  [$map]);
    }

    function it_throws_exception_for_yml_parsing_errors(
        Parser $parser,
        FactoryInterface $routeFactory,
        Map $map
    )
    {
        $file = __DIR__ . '/routes.yml';
        $parser->parse(file_get_contents($file))
            ->willThrow(new ParseException('test'));
        $this->beConstructedWith($file, $parser, $routeFactory);
        $this->shouldThrow(RoutesFileParserException::class)
            ->during('build',  [$map]);
    }
}