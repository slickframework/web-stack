<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Renderer\Extension;

use Slick\Template\EngineExtensionInterface;
use Slick\Template\Extension\AbstractTwigExtension;
use Slick\WebStack\Service\UriGeneratorInterface;
use Twig\TwigFunction;

/**
 * HtmlExtension
 *
 * @package Slick\WebStack\Renderer\Extension
 */
class HtmlExtension extends AbstractTwigExtension implements EngineExtensionInterface
{

    /**
     * @var UriGeneratorInterface
     */
    private $generator;

    /**
     * @var array Default options for addCss and addJs template functions
     */
    private static $options = [
        'is_safe' => ['html']
    ];

    /**
     * Creates an HTML template extension
     *
     * @param UriGeneratorInterface $generator
     */
    public function __construct(UriGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            'url'    => new TwigFunction('url', $this->urlClosure()),
            'addCss' => new TwigFunction('addCss', $this->cssClosure(), self::$options),
            'addJs'  => new TwigFunction('addJs', $this->jsClosure(), self::$options)
        ];
    }

    /**
     * Generates URI for provided location
     *
     * @param string $location Location name, path or identifier
     * @param array $options Filter options
     *
     * @return null|\Psr\Http\Message\UriInterface
     */
    private function location($location, array $options = [])
    {
        return $this->generator->generate($location, $options);
    }

    /**
     * Creates a css link tag for provided css file
     *
     * @param string $file
     * @param string $path
     * @param array $options
     *
     * @return string
     */
    private function css($file, $path = 'css', array $options = [])
    {
        $attr = array_key_exists('attr', $options) ? $options['attr'] : [];
        $attr = array_merge(['rel' => 'stylesheet'], $attr);
        $location = $this->location("{$path}/{$file}", $options);
        return sprintf(
            '<link href="%s" %s>',
            $location,
            $this->attributesStr($attr)
        );
    }

    /**
     * Converts an attributes key/value array to its string representation
     *
     * @param array $attr
     *
     * @return string
     */
    private function attributesStr(array $attr)
    {
        $output = [];
        foreach ($attr as $name => $value) {
            $output[] = "{$name}=\"{$value}\"";
        }
        return implode(' ', $output);
    }

    /**
     * Creates the url() template function closure
     *
     * @return \Closure
     */
    private function urlClosure()
    {
        return function ($location, array $options = []) {
            return $this->location($location, $options);
        };
    }

    /**
     * Creates the addCss() template function closure
     *
     * @return \Closure
     */
    private function cssClosure()
    {
        return function ($file, $path = 'css', array $options = []) {
            return $this->css($file, $path, $options);
        };
    }

    /**
     * Creates the addJs() template function closure
     *
     * @return \Closure
     */
    private function jsClosure()
    {
        return function ($file, $path = 'js', array $options = []) {
            $attr = array_key_exists('attr', $options)
                ? $options['attr']
                : [];
            $location = $this->location("{$path}/{$file}", $options);
            return sprintf(
                '<script src="%s" %s></script>',
                $location,
                $this->attributesStr($attr)
            );
        };
    }

}
