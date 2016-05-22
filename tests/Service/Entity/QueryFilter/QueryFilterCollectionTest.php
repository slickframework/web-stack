<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Service\Entity\QueryFilter;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Slick\Mvc\Service\Entity\QueryFilter\QueryFilterCollection;
use Slick\Mvc\Service\Entity\QueryFilterInterface;
use Slick\Orm\Repository\QueryObject\QueryObjectInterface;

/**
 * Query Filter Collection Test Case
 *
 * @package Slick\Tests\Mvc\Service\Entity\QueryFilter
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class QueryFilterCollectionTest extends TestCase
{

    /**
     * @var QueryFilterCollection
     */
    protected $filters;

    /**
     * Set the SUT collection object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->filters = new QueryFilterCollection();
    }

    /**
     * Should iterate over all filters and apply the filter to the query
     * @test
     */
    public function applyFilterToQuery()
    {
        /** @var QueryFilterInterface|MockObject $filter */
        $filter = $this->getMock(QueryFilterInterface::class);
        $filter->expects($this->once())
            ->method('apply');
        /** @var QueryObjectInterface $query */
        $query = $this->getMock(QueryObjectInterface::class);
        $this->filters->add($filter)
            ->apply($query);
    }
}
