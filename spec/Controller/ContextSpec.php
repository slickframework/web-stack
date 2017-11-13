<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Controller;

use Aura\Router\Route;
use Psr\Http\Message\ServerRequestInterface;
use Slick\WebStack\Controller\Context;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Controller\ControllerContextInterface;

/**
 * ContextSpec specs
 *
 * @package spec\Slick\WebStack\Controller
 */
class ContextSpec extends ObjectBehavior
{

    function let(ServerRequestInterface $request, Route $route)
    {
        $this->beConstructedWith($request, $route);
    }

    function its_a_controller_context()
    {
        $this->shouldBeAnInstanceOf(ControllerContextInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Context::class);
    }
}