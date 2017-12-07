<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Controller;

use Slick\WebStack\ControllerInterface;

/**
 * Trait Controller Methods
 *
 * @package Slick\WebStack\Controller
 */
trait ControllerMethods
{

    /**
     * @var ControllerContextInterface
     */
    protected $context;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Sets te context for this controller execution
     *
     * @param ControllerContextInterface $context
     *
     * @return self|ControllerInterface
     */
    public function runWithContext(ControllerContextInterface $context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Sets a variable to the view data model
     *
     * If you provide an associative array in the $name argument it will be
     * set all the elements using the key as the variable name on view
     * data model.
     *
     * @param string|array $name
     * @param mixed $value
     *
     * @return self|ControllerInterface
     */
    public function set($name, $value = null)
    {
        if (is_array($name)) {
            return $this->setValues($name);
        }
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * A view data model used by renderer
     *
     * @return array
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * Adds a multiple values from an associative array
     *
     * @param array $data
     *
     * @return self|ControllerInterface
     */
    private function setValues(array $data)
    {
        foreach ($data as $key => $datum) {
            $this->set($key, $datum);
        }
        return $this;
    }
}
