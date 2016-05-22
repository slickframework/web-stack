<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Utils;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Http\PhpEnvironment\Request;
use Slick\Mvc\Utils\Pagination;

/**
 * Pagination Test Case
 * 
 * @package Slick\Tests\Mvc\Utils
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class PaginationTest extends TestCase
{

    /**
     * @var Pagination
     */
    protected $pagination;

    /**
     * Sets the SUT pagination object
     */
    protected function setUp()
    {
        parent::setUp();
        $this->pagination = new Pagination(
            ['request' => $this->getMockedRequest()]
        );
    }

    /**
     * Should create a new request if none was given yet
     * @test
     */
    public function getRequest()
    {
        $pagination = new Pagination();
        $request = $pagination->getRequest();
        $this->assertInstanceOf(Request::class, $request);
    }
    
    /**
     * Should calculate and set the offset and total number of pages
     * @test
     */
    public function setTotalRecords()
    {
        $pagination = $this->pagination->setTotal(42);
        $this->assertSame($this->pagination, $pagination);
        return $pagination;
    }

    /**
     * Should divide the total rows by the number of rows per page.
     * The result is 4,333 witch should result in 5 pages.
     * 
     * @test
     * @depends setTotalRecords
     * 
     * @param Pagination $pagination
     */
    public function checkNumberOfPages(Pagination $pagination)
    {
        $this->assertEquals(5, $pagination->pages);
    }
    
    public function testOffset()
    {
        $this->assertEquals(10, $this->pagination->offset);
    }

    /**
     * Should maintain the query params
     * @test
     */
    public function getPageUrl()
    {
        $expected = '?rows=10&page=4';
        $this->assertEquals($expected, $this->pagination->pageUrl(4));
    }

    /**
     * @return Request|static
     */
    protected function getMockedRequest()
    {
        $request = new Request();
        $request = $request->withQueryParams(
            [
                'rows' => 10,
                'page' => 2
            ]
        );
        return $request;
    }
}
