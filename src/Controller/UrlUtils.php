<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Controller;

use Aura\Router\Exception as AuraException;
use Slick\Http\PhpEnvironment\Request;
use Slick\Mvc\Application;
use Slick\Mvc\Router;

/**
 * UrlUtils
 * 
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait UrlUtils
{
    /**
     * @return Request
     */
    abstract public function getRequest();

    /**
     * Gets the url for provided path
     * 
     * @param string $path
     * @return string
     */
    public function getUrl($path)
    {
        $regExp = '/\/\/|https?:/i';
        if (preg_match($regExp, $path)) {
            return $path;
        }
        try {
            $generated = call_user_func_array(
                [$this->getRouterGenerator(), 'generate'],
                func_get_args()
            );
        } catch (AuraException $caught) {
            $generated = false;
        }

        $basePath = rtrim($this->getRequest()->getBasePath(), '/');
        $path = $generated
            ? $generated
            : "{$basePath}/{$path}";

        return $path;
    }

    /**
     * Return Router path generator
     *
     * @return \Aura\Router\Generator
     */
    protected function getRouterGenerator()
    {
        /** @var Router $router */
        $router = Application::container()->get('router.middleware');
        return $router->getRouterContainer()->getGenerator();
    }
}