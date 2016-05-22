<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Service\Entity;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Database\Adapter\AdapterInterface;
use Slick\Mvc\Service\Entity\EntityListingService;
use Slick\Mvc\Service\Entity\QueryFilter\QueryFilterCollection;
use Slick\Mvc\Utils\Pagination;
use Slick\Orm\Entity\EntityCollection;
use Slick\Orm\Orm;
use Slick\Orm\Repository\QueryObject\QueryObject;
use Slick\Orm\RepositoryInterface;
use Slick\Tests\Mvc\Fixtures\Domain\Post;

/**
 * Entity Listing Service Test Case
 * 
 * @package Slick\Tests\Mvc\Service\Entity
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class EntityListingServiceTest extends TestCase
{

    /**
     * @var EntityListingService
     */
    private $service;

    /**
     * @var EntityCollection
     */
    private $collection;

    /**
     * Set the SUT service object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->service = new EntityListingService(Post::class);
        /** @var AdapterInterface $adapter */
        $adapter = $this->getMock(AdapterInterface::class);
        Orm::getInstance()->setAdapter('default', $adapter);
    }

    /**
     * Should get a default pagination if none is given
     */
    public function testGetPagination()
    {
        $pagination = $this->service->getPagination();
        $this->assertInstanceOf(Pagination::class, $pagination);
    }

    /**
     * Should get a filter collection if none is given
     */
    public function testGetFilters()
    {
        $filters = $this->service->getFilters();
        $this->assertInstanceOf(QueryFilterCollection::class, $filters);
    }

    /**
     * Should get a Post entity repository
     */
    public function testGetRepository()
    {
        $repo = $this->service->getRepository();
        $this->assertInstanceOf(RepositoryInterface::class, $repo);
        $this->assertEquals(
            Post::class,
            $repo->getEntityDescriptor()->className()
        );
    }

    /**
     * Should run the query, with pagination and filters
     * @test 
     */
    public function testGetList()
    {
        $this->service->setRepository($this->getRepositoryStub());
        $data = $this->service->getList();
        $this->assertEquals($this->collection, $data);
    }
    
    /**
     * Get repository
     * 
     * @return MockObject|RepositoryInterface
     */
    private function getRepositoryStub()
    {
        /** @var RepositoryInterface|MockObject $repository */
        $repository = $this->getMock(RepositoryInterface::class);
        $repository->method('find')
            ->willReturn($this->getQueryMock());
        return $repository;
    }

    /**
     * @return MockObject|QueryObject
     */
    private function getQueryMock()
    {
        /** @var QueryObject|MockObject $query */
        $query = $this->getMockBuilder(QueryObject::class)
            ->disableOriginalConstructor()
            ->setMethods(['all', 'count', 'limit'])
            ->getMock();
        $query->expects($this->at(0))
            ->method('count')
            ->willReturn(2);
        $query->expects($this->at(1))
            ->method('limit')
            ->with(0, 12);
        $query->expects($this->at(2))
            ->method('all')
            ->willReturn($this->getPostCollection());
        return $query;
    }

    /**
     * Get query result
     * 
     * @return EntityCollection
     */
    private function getPostCollection()
    {
        if (null == $this->collection) {
            $data = [
                new Post(['id' => 1]),
                new Post(['id' => 2]),
            ];
            $this->collection = new EntityCollection($data);
        }
        return $this->collection;
    }
}
