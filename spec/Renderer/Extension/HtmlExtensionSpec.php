<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Renderer\Extension;

use Slick\Template\EngineExtensionInterface;
use Slick\WebStack\Renderer\Extension\HtmlExtension;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Service\UriGeneratorInterface;

/**
 * HtmlExtensionSpec specs
 *
 * @package spec\Slick\WebStack\Renderer\Extension
 */
class HtmlExtensionSpec extends ObjectBehavior
{

    function let(UriGeneratorInterface $uriGenerator)
    {
        $this->beConstructedWith($uriGenerator);
    }

    function its_a_template_extension()
    {
        $this->shouldBeAnInstanceOf(EngineExtensionInterface::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(HtmlExtension::class);
    }

    
}