<?php

namespace spec\Slick\WebStack\Http\Renderer;

use Aura\Router\Route;
use Slick\WebStack\Http\Renderer\ViewInflector;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Slick\WebStack\Http\Renderer\ViewInflectorInterface;

/**
 * ViewInflectorSpec
 *
 * @package spec\Slick\WebStack\Http\Renderer
 * @author  Filipe Silva <filipe.silva@sata.pt>
 */
class ViewInflectorSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith('tpl');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ViewInflector::class);
    }

    function its_a_view_inflector()
    {
        $this->shouldBeAnInstanceOf(ViewInflectorInterface::class);
    }

    function it_inflates_camel_cased_names_to_dashed_names()
    {
        $route = new Route();
        $route->attributes([
            'controller' => 'staticPages',
            'action' => 'aboutUs'
        ]);
        $this->inflect($route)->shouldBe('static-pages/about-us.tpl');
    }

    function it_inflates_underscored_names_to_dashed_names()
    {
        $this->beConstructedWith('html');
        $route = new Route();
        $route->attributes([
            'controller' => 'otherPages',
            'action' => 'testPage'
        ]);
        $this->inflect($route)->shouldBe('other-pages/test-page.html');
    }
}
