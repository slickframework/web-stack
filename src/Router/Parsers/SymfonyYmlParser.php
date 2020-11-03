<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Router\Parsers;

use Slick\WebStack\Router\RoutesParser;
use Symfony\Component\Yaml\Parser;

/**
 * SymfonyYmlParser
 *
 * @package Slick\WebStack\Router\Parsers
 */
class SymfonyYmlParser implements RoutesParser
{
    /**
     * @var Parser
     */
    private $parser;


    /**
     * Creates a SymfonyYmlParser
     *
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * parse
     *
     * @param string $content
     * @return array|mixed|\stdClass|\Symfony\Component\Yaml\Tag\TaggedValue|null
     */
    public function parse(string $content)
    {
        return $this->parser->parse($content);
    }

    /**
     * parseFile
     *
     * @param string $filename
     * @return array|mixed|\stdClass|\Symfony\Component\Yaml\Tag\TaggedValue|null
     */
    public function parseFile(string $filename)
    {
        return $this->parser->parseFile($filename);
    }
}
