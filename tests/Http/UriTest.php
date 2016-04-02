<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Http;

use PHPUnit_Framework_TestCase as TestCase;
use Slick\Mvc\Http\Uri;

/**
 * Uri Test Case
 *
 * @package Slick\Tests\Mvc\Http
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class UriTest extends TestCase
{

    /**
     * Should create the URL based on a PSR-7 UriInterface and fix the path
     * @test
     */
    public function createFixedPathUri()
    {
        $uri = Uri::create(new \Slick\Http\Uri('http://example.com/'));
        $this->assertEquals('/', $uri->getPath());
    }
}
