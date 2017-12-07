<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Http\Server;

use Interop\Http\Server\MiddlewareInterface;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Http\Server\FlashMessagesMiddleware;
use Slick\WebStack\Service\FlashMessages;

/**
 * FlashMessagesMiddlewareSpec specs
 *
 * @package spec\Slick\WebStack\Http\Server
 */
class FlashMessagesMiddlewareSpec extends ObjectBehavior
{
    function let(FlashMessages $flashMessages)
    {
        $this->beConstructedWith($flashMessages);
    }

    function its_an_http_server_middleware()
    {
        $this->shouldBeAnInstanceOf(MiddlewareInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FlashMessagesMiddleware::class);
    }
}