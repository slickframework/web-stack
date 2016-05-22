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
use Slick\Mvc\Service\Entity\QueryFilter\SearchFilter;
use Slick\Orm\Repository\QueryObject\QueryObjectInterface;

/**
 * Search Filter Test
 * 
 * @package Slick\Tests\Mvc\Service\Entity\QueryFilter
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class SearchFilterTest extends TestCase
{

    /**
     * @var SearchFilter
     */
    private $filter;
    
    protected function setUp()
    {
        parent::setUp();
        $this->filter = new SearchFilter([
            'fields' => ['name', 'description'],
            'pattern' => 'test'
        ]);
    }
    
    public function testFilter()
    {
        $expected = [
            'name LIKE :pattern AND description LIKE :pattern' => [
                ':pattern' => "%test%"
            ]
        ];
        /** @var QueryObjectInterface|MockObject $query */
        $query = $this->getMock(QueryObjectInterface::class);
        $query->expects($this->once())
            ->method('where')
            ->with($expected);
        $this->filter->apply($query);
    }
    
    public function testEmptyFields()
    {
        /** @var QueryObjectInterface|MockObject $query */
        $query = $this->getMock(QueryObjectInterface::class);
        $query->expects($this->never())
            ->method('where');
        $this->filter->fields = [];
        $this->filter->apply($query);
    }
}
