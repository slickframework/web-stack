<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Utils;

use Slick\Common\Base;
use Slick\Filter\StaticFilter;
use Slick\Http\PhpEnvironment\Request;
use Slick\Validator\StaticValidator;

/**
 * Pagination utility
 *
 * @package Slick\Mvc\Utils
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 *
 * @property Request $request
 * @property integer $offset
 * @property integer $rowsPerPage
 * @property integer $pages
 * @property integer $total
 * @property integer $current
 */
class Pagination extends Base
{

    /**
     * @readwrite
     * @var int Total pages
     */
    protected $pages = 0;
    /**
     * @readwrite
     * @var int Total records
     */
    protected $total;
    /**
     * @readwrite
     * @var int current page index
     */
    protected $current = 1;
    /**
     * @readwrite
     * @var int total rows per page
     */
    protected $rowsPerPage = 12;
    /**
     * @readwrite
     * @var int First row to return
     */
    protected $offset = 0;
    /**
     * @readwrite
     * @var Request
     */
    protected $request;

    /**
     * Overrides the constructor to calculate the properties for current
     * pagination state.
     *
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);
        if ($this->getRequest()->getQuery('rows')) {
            $this->setRowsPerPage($this->getRequest()->getQuery('rows'));
        }
        if ($this->getRequest()->getQuery('page')) {
            $this->setCurrent($this->getRequest()->getQuery('page'));
        }
    }

    /**
     * Lazy loads request object
     *
     * @return Request
     */
    public function getRequest()
    {
        if (is_null($this->request)) {
            $this->request = new Request();
        }
        return $this->request;
    }
    
    /**
     * Sets current page and calculates offset
     *
     * @param integer $value The number of the current page.
     *
     * @return Pagination A self instance for method chain calls
     */
    public function setCurrent($value)
    {
        if (StaticValidator::validates('number', $value)) {
            $this->current = StaticFilter::filter('number',$value);
            $this->offset = $this->rowsPerPage * ($this->current - 1);
        }
        return $this;
    }
    
    /**
     * Sets the total rows to paginate.
     *
     * @param integer $value The rows total to set.
     *
     * @return Pagination A self instance for method chain calls
     */
    public function setTotal($value)
    {
        if (StaticValidator::validates('number', $value)) {
            $this->total = StaticFilter::filter('number',$value);
            $this->pages = ceil($this->total / $this->rowsPerPage);
        }
        return $this;
    }
    
    /**
     * Sets the total rows per page.
     *
     * @param integer $value The total rows per page to set.
     *
     * @return Pagination A self instance for method chain calls
     */
    public function setRowsPerPage($value)
    {
        if (StaticValidator::validates('number', $value)) {
            $this->rowsPerPage = StaticFilter::filter('number',$value);
            $this->pages = ceil($this->total / $this->rowsPerPage);
        }
        return $this;
    }
    
    /**
     * Creates a request query for the provided page.
     *
     * This method check the current request query in order to maintain the
     * other parameters unchanged and sets the 'page' parameter to the
     * provided page number.
     *
     * @param integer $page The page number to build the query on.
     *
     * @return string The query string to use in the pagination links.
     */
    public function pageUrl($page)
    {
        $params = $this->getRequest()->getQuery();
        if (isset($params['url']))
            unset($params['url']);
        $params['page'] = $page;
        return '?' . http_build_query($params);
    }
}