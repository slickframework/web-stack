<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Command\Task\AskForNamespace;

use Slick\Common\Utils\Collection\AbstractCollection;
use Slick\Common\Utils\CollectionInterface;

/**
 * NameSpaceCollection
 *
 * @package Slick\WebStack\Console\Command\Task\AskForNamespace
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class NameSpaceCollection extends AbstractCollection implements
    CollectionInterface
{

    public function offsetSet($offset, $value)
    {
        $this->add($value);
    }

    /**
     * Adds an item to the collection
     *
     * @param NameSpaceEntry $nameSpace
     *
     * @return NameSpaceCollection
     */
    public function add(NameSpaceEntry $nameSpace)
    {
        $this->data[] = $nameSpace;
        return $this;
    }
}