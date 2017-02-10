<?php

namespace spec\Slick\WebStack\Http\UrlRewriter;

use Psr\Http\Message\UriInterface;
use Slick\WebStack\Http\UrlRewriter\Uri;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UriSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Uri::class);
    }

    function its_a_psr7_uri()
    {
        $this->shouldBeAnInstanceOf(UriInterface::class);
    }

    function it_adds_a_base_dash_to_the_uri_path()
    {
        $this->beConstructedWith('the/path.html?foo=bar#test');
        $this->getPath()->shouldBe('/the/path.html');
    }
}
