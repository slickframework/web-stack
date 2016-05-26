<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Controller;

use PHPUnit_Framework_TestCase as TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Http\PhpEnvironment\Request;
use Slick\I18n\TranslateMethods;
use Slick\Mvc\Controller\EntityBasedMethods;
use Slick\Mvc\Controller\EntityListingMethods;
use Slick\Mvc\ControllerInterface;
use Slick\Mvc\Service\Entity\EntityListingService;
use Slick\Mvc\Service\Entity\QueryFilter\SearchFilter;
use Slick\Mvc\Service\Entity\QueryFilterCollectionInterface;
use Slick\Mvc\Utils\Pagination;
use Slick\Orm\Orm;
use Slick\Tests\Mvc\Fixtures\Domain\Post;

/**
 * Entity Listing Methods Test
 * 
 * @package Slick\Tests\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityListingMethodsTest extends TestCase
{
    
    /**
     * @var array
     */
    private $viewVars = [];

    /**
     * @var string
     */
    protected $entityClassName = Post::class;
    
    use EntityListingMethods;
    
    use EntityBasedMethods;
    
    use TranslateMethods;
    
    public function testNamePlural()
    {
        $this->assertEquals('posts', $this->getEntityNamePlural());
    }
    
    public function testListingServiceLazyLoading()
    {
        $service = $this->getListingService();
        $this->assertInstanceOf(EntityListingService::class, $service);
    }

    
    public function testGetSearchFields()
    {
        /** @var AdapterInterface $adapter */
        $adapter = $this->getMock(AdapterInterface::class);
        Orm::getInstance()->setAdapter('default', $adapter);
        $this->assertEquals(['posts.name'], $this->getSearchFields());
    }
    
    public function testGetSearchFilter()
    {
        $searchFilter = $this->getSearchFilter();
        $this->assertInstanceOf(SearchFilter::class, $searchFilter);
    }
    
    public function testGetPagination()
    {
        $pagination = $this->getPagination();
        $this->assertInstanceOf(Pagination::class, $pagination);
    }

    public function testIndex()
    {
        $service = $this->getMockBuilder(EntityListingService::class)
            ->disableOriginalConstructor()
            ->setMethods(
                ['setPagination', 'getFilters', 'getList', 'getPagination']
            )
            ->getMock();
        $service->expects($this->at(0))
            ->method('setPagination')
            ->with($this->getPagination())
            ->willReturn($service);
        $service->expects($this->at(1))
            ->method('getFilters')
            ->willReturn($this->getFiltersMock());
        $service->expects($this->once())
            ->method('getList')
            ->willReturn([]);
        $service->expects($this->once())
            ->method('getPagination')
            ->willReturn($this->pagination);
        $this->listingService = $service;
        $this->index();
        $this->assertEquals([], $this->viewVars['posts']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getFiltersMock()
    {
        $filters = $this->getMock(QueryFilterCollectionInterface::class);
        $filters->expects($this->once())
            ->method('add')
            ->with($this->isInstanceOf(SearchFilter::class))
            ->willReturnSelf();
        return $filters;
    }

    /**
     * Sets a value to be used by render
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return void
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->viewVars = array_merge($this->viewVars, $key);
            return;
        }

        $this->viewVars[$key] = $value;
    }

    /**
     * Gets updated HTTP request
     *
     * @return ServerRequestInterface|Request
     */
    public function getRequest()
    {
        return new Request();
    }

    /**
     * Gets the entity FQ class name
     *
     * @return string
     */
    public function getEntityClassName()
    {
        return $this->entityClassName;
    }

    /**
     * Redirects the flow to another route/path
     *
     * @param string $path the route or path to redirect to
     *
     * @return ControllerInterface|self|$this
     */
    public function redirect($path)
    {
        return $this;
    }

    /**
     * Gets the URL base path form this controller
     *
     * @return string
     */
    protected function getBasePath()
    {
        return 'base-path';
    }

}
