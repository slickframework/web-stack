<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Service\Entity\QueryFilter;

use Slick\Common\Utils\Collection\AbstractCollection;
use Slick\Common\Utils\CollectionInterface;
use Slick\Mvc\Service\Entity\QueryFilterCollectionInterface;
use Slick\Mvc\Service\Entity\QueryFilterInterface;
use Slick\Orm\Repository\QueryObject\QueryObjectInterface;

/**
 * Query Filter Collection
 * 
 * @package Slick\Mvc\Service\Entity\QueryFilter
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class QueryFilterCollection extends AbstractCollection implements 
    QueryFilterCollectionInterface,
    CollectionInterface
{

    /**
     * Add a filter to this filter collection
     *
     * @param QueryFilterInterface $filter
     *
     * @return $this|self|QueryFilterCollectionInterface
     */
    public function add(QueryFilterInterface $filter)
    {
        $this->data[] = $filter;
        return $this;
    }

    /**
     * Applies the filter to the provided query
     *
     * @param QueryObjectInterface $query
     *
     * @return $this|self|QueryFilterCollectionInterface
     */
    public function apply(QueryObjectInterface $query)
    {
        /** @var QueryFilterInterface $filter */
        foreach ($this->data as $filter) {
            $filter->apply($query);
        }
        return $this;
    }
}