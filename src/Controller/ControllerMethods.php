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

    protected ?ControllerContextInterface $context = null;
    protected array $data = [];

    /**
     * Sets te context for this controller execution
     *
     * @param ControllerContextInterface $context
     *
     * @return ControllerInterface
     */
    public function runWithContext(ControllerContextInterface $context): ControllerInterface
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
     * @return ControllerInterface
     */
    public function set(string|array $name, mixed $value = null): ControllerInterface
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
    public function data(): array
    {
        return $this->data;
    }

    /**
     * Adds a multiple values from an associative array
     *
     * @param array $data
     *
     * @return ControllerInterface
     */
    private function setValues(array $data): ControllerInterface
    {
        foreach ($data as $key => $datum) {
            $this->set($key, $datum);
        }
        return $this;
    }
}
