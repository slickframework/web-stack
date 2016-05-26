<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Service\Entity;

use Slick\Common\Utils\CollectionInterface;
use Slick\Database\Sql\Select;
use Slick\Mvc\Service\Entity\QueryFilter\QueryFilterCollection;
use Slick\Mvc\Service\EntityServiceInterface;
use Slick\Mvc\Utils\Pagination;
use Slick\Orm\EntityInterface;
use Slick\Orm\Orm;
use Slick\Orm\Repository\QueryObject\QueryObjectInterface;
use Slick\Orm\RepositoryInterface;

/**
 * Entity Listing Service
 * 
 * @package Slick\Mvc\Service\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityListingService extends AbstractEntityService implements
    EntityServiceInterface
{

    /**
     * @var Pagination
     */
    private $pagination;

    /**
     * @var QueryFilterInterface[]|QueryFilterCollectionInterface
     */
    private $filters;

    /**
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * @var string
     */
    private $order;

    /**
     * Entity Listing Service needs an entity or entity class name.
     * 
     * @param string|EntityInterface $className
     */
    public function __construct($className)
    {
        $method = 'setEntity';
        if (!$className instanceof EntityInterface) {
            $method = 'setEntityClass';
        }
        $this->$method($className);
    }

    /**
     * Get a paginated list of entities
     * 
     * @return \Slick\Orm\Entity\EntityCollection
     */
    public function getList()
    {
        /** @var Select|QueryObjectInterface $query */
        $query = $this->getRepository()->find();
        $this->getFilters()->apply($query);
        $query->order($this->getOrder());
        $this->getPagination()->setTotal($query->count());
        $query->limit(
            $this->getPagination()->offset,
            $this->getPagination()->rowsPerPage
        );
        return $query->all();
    }

    /**
     * Gets the pagination object
     * 
     * @return Pagination
     */
    public function getPagination()
    {
        if (null == $this->pagination) {
            $this->setPagination(new Pagination());
        }
        return $this->pagination;
    }

    /**
     * Set pagination
     * 
     * @param Pagination $pagination
     * 
     * @return EntityListingService
     */
    public function setPagination($pagination)
    {
        $this->pagination = $pagination;
        return $this;
    }

    /**
     * Get query filters collection
     * 
     * @return CollectionInterface|QueryFilterCollectionInterface
     */
    public function getFilters()
    {
        if (null == $this->filters) {
            $this->setFilters(new QueryFilterCollection());
        }
        return $this->filters;
    }

    /**
     * Set filters collection
     * 
     * @param QueryFilterCollectionInterface $filters
     * 
     * @return EntityListingService
     */
    public function setFilters(QueryFilterCollectionInterface $filters)
    {
        $this->filters = $filters;
        return $this;
    }

    /**
     * Get entity query
     * 
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        if (null == $this->repository) {
            $this->setRepository(
                Orm::getRepository($this->getEntityClassName())
            );
        }
        return $this->repository;
    }

    /**
     * Set entity repository
     * 
     * @param RepositoryInterface $repository
     * 
     * @return EntityListingService
     */
    public function setRepository(RepositoryInterface $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * Get the order by clause
     *
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set the order by clause
     *
     * @param string $order
     *
     * @return EntityListingService
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }
    
}