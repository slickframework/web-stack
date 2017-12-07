<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Renderer;

use Aura\Router\Route;
use Slick\WebStack\Renderer\ViewInflector;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Renderer\ViewInflectorInterface;

/**
 * ViewInflectorSpec specs
 *
 * @package spec\Slick\WebStack\Renderer
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