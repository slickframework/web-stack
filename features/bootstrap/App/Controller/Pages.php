<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Slick\Mvc\Controller;

/**
 * Pages controller
 * 
 * @package App\Controller
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class Pages extends Controller
{

    /**
     * Home page handler
     */
    public function home()
    {
        $this->set('str', 'This is home!');
    }
}