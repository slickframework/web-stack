<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Slick\Common\Utils\Text;
use Slick\Http\PhpEnvironment\Request;
use Slick\Mvc\ControllerInterface;

/**
 * CRUD Common Methods
 * 
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait CrudCommonMethods
{
    
    /**
     * Get the plural name of the entity
     *
     * @return string
     */
    protected function getEntityNamePlural()
    {
        $name = $this->getEntityNameSingular();
        $nameParts = Text::camelCaseToSeparator($name, '#');
        $nameParts = explode('#', $nameParts);
        $lastPart = array_pop($nameParts);
        $lastPart = ucfirst(Text::plural(strtolower($lastPart)));
        array_push($nameParts, $lastPart);
        return lcfirst(implode('', $nameParts));
    }

    /**
     * Get entity singular name used on controller actions
     * 
     * @return string
     */
    protected function getEntityNameSingular()
    {
        $names = explode('\\', $this->getEntityClassName());
        $name = end($names);
        return lcfirst($name);
    }

    /**
     * Gets updated HTTP request
     *
     * @return ServerRequestInterface|Request
     */
    abstract public function getRequest();

    /**
     * Sets a value to be used by render
     *
     * The key argument can be an associative array with values to be set
     * or a string naming the passed value. If an array is given then the
     * value will be ignored.
     *
     * Those values must be set in the request attributes so they can be used
     * latter by any other middle ware in the stack.
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return ControllerInterface
     */
    abstract public function set($key, $value = null);

    /**
     * Gets the entity FQ class name
     *
     * @return string
     */
    abstract public function getEntityClassName();

    /**
     * Redirects the flow to another route/path
     *
     * @param string $path the route or path to redirect to
     *
     * @return ControllerInterface|self|$this
     */
    abstract public function redirect($path);
}