<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Renderer;

use Slick\Http\PhpEnvironment\Request;
use Slick\Mvc\Application;
use Slick\Mvc\Controller\UrlUtils;
use Slick\Template\EngineExtensionInterface;
use Slick\Template\Extension\AbstractTwigExtension;
use Twig_SimpleFunction as SimpleFunction;

/**
 * HTML twig extension
 *
 * @package Slick\Mvc\Renderer
 */
class HtmlExtension extends AbstractTwigExtension implements EngineExtensionInterface
{
    /**
     * For url parse
     */
    use UrlUtils;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return "HTML helper extension";
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return [
            new SimpleFunction('url', function($url) {
                $args = func_get_args();
                return call_user_func_array([$this, 'getUrl'], $args);
            }),
            new SimpleFunction(
                'addCss',
                function($file, $path='/css', $attr = []) {
                    return $this->addCss($file, $path, $attr);
                }
            ),
            new SimpleFunction(
                'addJs',
                function($file, $path='/javascripts') {
                    return $this->addJs($file, $path);
                }
            )
        ];
    }

    /**
     * Get request
     * @return Request
     */
    public function getRequest()
    {
        if (null == $this->request) {
            $this->setRequest(Application::container()->get('request'));
        }
        return $this->request;
    }

    /**
     * Set HTML request message
     * 
     * @param Request $request
     * 
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Creates an HTML style tag
     *
     * @param string $file
     * @param string $path
     * @param array $attr
     *
     * @return string
     */
    public function addCss($file, $path='/css', $attr = [])
    {
        $attr = array_merge(['rel' => 'stylesheet'], $attr);
        $output = [];
        foreach ($attr as $name => $value) {
            $output[] = "{$name}=\"{$value}\"";
        }
        $attr = implode(' ', $output);
        $file = str_replace('//', '', "{$path}/{$file}");
        return sprintf('<link href="%s" %s>', $file, $attr);
    }

    public function addJs($file, $path='/javascripts')
    {
        $file = str_replace('//', '', "{$path}/{$file}");
        return sprintf('<script src="%s"></script>', $file);
    }
}