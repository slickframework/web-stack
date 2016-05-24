<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Controller;

use Slick\Filter\StaticFilter;
use Slick\Mvc\Service\Entity\EntityListingService;
use Slick\Mvc\Service\Entity\QueryFilter\SearchFilter;
use Slick\Mvc\Utils\Pagination;

/**
 * Entity Listing Methods
 *
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait EntityListingMethods
{

    use EntityBasedMethods;
    
    /**
     * @var int
     */
    protected $rowsPerPage = 12;

    /**
     * @var Pagination
     */
    protected $pagination;

    /**
     * @var EntityListingService
     */
    protected $listingService;

    /**
     * @var string[]
     */
    protected $searchFields;

    /**
     * @var string
     */
    protected $order;

    /**
     * Handle the request to display a list of entities
     */
    public function index()
    {
        $this->getListingService()
            ->setPagination($this->getPagination())
            ->getFilters()->add($this->getSearchFilter());
        $this->set(
            [
                $this->getEntityNamePlural() => $this->getListingService()
                    ->getList(),
                'pagination' => $this->getListingService()->getPagination()
            ]
        );
    }

    /**
     * Get pagination for roes per page property
     *
     * @return Pagination
     */
    protected function getPagination()
    {
        if (null == $this->pagination) {
            $this->pagination = new Pagination(
                [
                    'rowsPerPage' => $this->rowsPerPage,
                    'request' => $this->getRequest()
                ]
            );
        }
        return $this->pagination;
    }

    /**
     * Get the entity listing service
     *
     * @return EntityListingService
     */
    protected function getListingService()
    {
        if (null == $this->listingService) {
            $this->listingService = new EntityListingService(
                $this->getEntityClassName()
            );
        }
        return $this->listingService;
    }

    /**
     * Get search filter 
     * 
     * @return SearchFilter
     */
    protected function getSearchFilter()
    {
        $pattern = $this->getRequest()->getQuery('pattern', null);
        $pattern = StaticFilter::filter('text', $pattern);
        $this->set('pattern', $pattern);
        
        return new SearchFilter(['pattern' => $pattern]);
    }

    /**
     * Get the fields list to use on search filter
     * 
     * @return array|\string[]
     */
    protected function getSearchFields()
    {
        if (null == $this->searchFields) {
            $field = $this->getEntityDescriptor()->getDisplayFiled();
            $this->searchFields = [
                $this->getEntityDescriptor()
                    ->getTableName().'.'.$field->getField()
            ];
        }
        return $this->searchFields;
    }
}