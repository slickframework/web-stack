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
use Slick\Filter\StaticFilter;
use Slick\Http\PhpEnvironment\Request;
use Slick\Mvc\ControllerInterface;
use Slick\Mvc\Service\Entity\EntityListingService;
use Slick\Mvc\Service\Entity\QueryFilter\SearchFilter;
use Slick\Mvc\Utils\Pagination;
use Slick\Orm\Orm;

/**
 * Entity Listing Methods
 *
 * @package Slick\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
trait EntityListingMethods
{

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
                $this->entityClassName
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
     * Get the plural name of the entity
     * 
     * @return string
     */
    protected function getEntityNamePlural()
    {
        $names = explode('\\', $this->entityClassName);
        $name = end($names);
        $nameParts = Text::camelCaseToSeparator($name, '#');
        $nameParts = explode('#', $nameParts);
        $lastPart = array_pop($nameParts);
        $lastPart = ucfirst(Text::plural(strtolower($lastPart)));
        array_push($nameParts, $lastPart);
        return lcfirst(implode('', $nameParts));
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
    abstract protected function getEntityClassName();

    /**
     * Get the fields list to use on search filter
     * 
     * @return array|\string[]
     */
    protected function getSearchFields()
    {
        if (null == $this->searchFields) {
            $descriptor =  $this->getListingService()
                ->getRepository()
                ->getEntityDescriptor()
            ;    
            $field = $descriptor->getDisplayFiled();
            $this->searchFields = [
                $descriptor->getTableName().'.'.$field->getField()
            ];
        }
        return $this->searchFields;
    }
}