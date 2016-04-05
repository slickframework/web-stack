<?php
/**
 * Created by PhpStorm.
 * User: filipesilva
 * Date: 04/04/16
 * Time: 22:10
 */

namespace Slick\Mvc\Controller;


use Aura\Router\Exception as AuraException;
use Slick\Http\PhpEnvironment\Request;
use Slick\Mvc\Application;
use Slick\Mvc\Router;

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

        $path = $generated
            ? $generated
            : $path;
        $basePath = rtrim($this->getRequest()->getBasePath(), '/');
        return ("{$basePath}/{$path}");
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