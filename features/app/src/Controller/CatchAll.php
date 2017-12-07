<?php
/**
 * Created by PhpStorm.
 * User: fsilva
 * Date: 06-12-2017
 * Time: 16:17
 */

namespace Features\App\Controller;

use Slick\WebStack\Controller;

/**
 * Catch All Controller
 *
 * @package Features\App\Controller
 */
class CatchAll extends Controller
{

    public function showState($arg)
    {
        $this->set(compact('arg'));
    }
}
