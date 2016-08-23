<?php

/**
 * This file is part of Mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\MetaDataGenerator;

use Slick\Common\Utils\Collection\AbstractCollection;
use Slick\Common\Utils\CollectionInterface;
use Slick\Mvc\Console\MetaDataGeneratorInterface;

/**
 * Controller Meta data generator
 *
 * @package Slick\Mvc\Console\MetaDataGenerator
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
class Controller extends AbstractCollection implements MetaDataGeneratorInterface, CollectionInterface
{

    /**
     * For input/output getters and setters
     */
    use ConsoleAwareMethods;

    /**
     * Adds a new generator to the generators collection
     *
     * @param MetaDataGeneratorInterface $generator
     *
     * @return self|Controller
     */
    public function add(MetaDataGeneratorInterface $generator)
    {
        $generator->setInput($this->getInput())
            ->setOutput($this->getOutput())
            ->setCommand($this->getCommand());
        array_push($this->data, $generator);
        return $this;
    }

    /**
     * Get generated data array
     *
     * @return array
     */
    public function getData()
    {
        $data = [];
        /** @var MetaDataGeneratorInterface $generator */
        foreach ($this->data as $generator) {
            $data = array_merge($data, $generator->getData());
        }
        return $data;
    }

    /**
     * Overrides default assignment to force data generator objects only
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->add($value);
    }
}