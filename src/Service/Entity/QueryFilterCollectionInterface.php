<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Service\Entity;

use Slick\Orm\Repository\QueryObject\QueryObjectInterface;

/**
 * Query Filter Collection Interface
 * 
 * @package Slick\Mvc\Service\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
interface QueryFilterCollectionInterface
{

    /**
     * Add a filter to this filter collection
     * 
     * @param QueryFilterInterface $filter
     * 
     * @return $this|self|QueryFilterCollectionInterface
     */
    public function add(QueryFilterInterface $filter);

    /**
     * Applies the filter to the provided query
     *
     * @param QueryObjectInterface $query
     *
     * @return $this|self|QueryFilterCollectionInterface
     */
    public function apply(QueryObjectInterface $query);
}