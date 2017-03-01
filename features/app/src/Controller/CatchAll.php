<?php

/**
 * This file is part of WebStack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Features\App\Controller;


use Slick\WebStack\Controller;

class CatchAll extends Controller
{

    public function showState($arg)
    {
        $this->set(compact('arg'));
    }
}
