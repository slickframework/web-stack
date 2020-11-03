<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Slick\WebStack\Router\Parsers;

use Slick\WebStack\Router\Parsers\SymfonyYmlParser;
use PhpSpec\ObjectBehavior;
use Slick\WebStack\Router\RoutesParser;
use Symfony\Component\Yaml\Parser;

/**
 * SymfonyYmlParserSpec specs
 *
 * @package spec\Slick\WebStack\Router\Parsers
 */
class SymfonyYmlParserSpec extends ObjectBehavior
{

    function let(Parser $parser)
    {
        $this->beConstructedWith($parser);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SymfonyYmlParser::class);
    }

    function its_a_routes_parser()
    {
        $this->shouldBeAnInstanceOf(RoutesParser::class);
    }

    function it_parses_a_yml_file(Parser $parser)
    {
        $file = dirname(__DIR__).'/routes.yml';
        $this->parseFile($file);
        $parser->parseFile($file)->shouldHaveBeenCalled();
    }
}