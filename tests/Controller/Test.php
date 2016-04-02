<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Tests\Mvc\Controller;

use Slick\Mvc\Controller;

/**
 * Test controller
 *
 * @package Slick\Tests\Mvc\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Test extends Controller
{

    public function index()
    {
        $this->set('test', 'Test::index()');
    }

    public function otherMethod($arg)
    {
        $this->set('test', "Test::otherMethod($arg)");
    }
}