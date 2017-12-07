<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Router\Builder;

use Aura\Router\Map;
use Aura\Router\Route;
use Slick\WebStack\Router\Builder\FactoryInterface;
use Slick\WebStack\Router\Builder\RouteFactory;
use PhpSpec\ObjectBehavior;

/**
 * RouteFactorySpec specs
 *
 * @package spec\Slick\WebStack\Router\Builder
 */
class RouteFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(RouteFactory::class);
    }
    function its_a_route_factory()
    {
        $this->shouldBeAnInstanceOf(FactoryInterface::class);
    }
    function it_parses_simple_route_definitions(
        Map $map,
        Route $route
    )
    {
        $map->get('blog.read', '/blog/{id}')->willReturn($route);
        $this->parse('blog.read', '/blog/{id}', $map)->shouldBe($route);
        $map->get('blog.read', '/blog/{id}')->shouldHaveBeenCalled();
    }
    function it_can_parse_complex_route_definitions(
        Map $map,
        Route $route
    )
    {
        $data = [
            'name' => 'blog.edit',
            'data' => [
                'method' => 'PUT',
                'allows' => ['POST'],
                'path' => '/blog/{id}/edit'
            ],
        ];
        $map->route($data['name'], $data['data']['path'])->willReturn($route);
        $this->parse($data['name'], $data['data'], $map)->shouldBe($route);
        $map->route($data['name'], $data['data']['path'])->shouldHaveBeenCalled();
        $route->allows(['PUT', 'POST'])->shouldHaveBeenCalled();
    }
    function it_can_add_all_known_route_properties(
        Map $map,
        Route $route
    )
    {
        $data = [
            'name' => 'blog.edit',
            'data' => [
                'defaults' => [
                    'id' => null
                ],
                'host' => 'www.example.com',
                'path' => '/blog/{id}/edit',
                'allows' => ['GET', 'POST']
            ],
        ];
        $map->route($data['name'], $data['data']['path'])->willReturn($route);
        $this->parse($data['name'], $data['data'], $map)->shouldBe($route);
        $route->defaults($data['data']['defaults'])->shouldHaveBeenCalled();
        $route->host($data['data']['host'])->shouldHaveBeenCalled();
    }

}