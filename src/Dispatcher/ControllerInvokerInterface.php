<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Dispatcher;

use Slick\WebStack\ControllerInterface;

/**
 * Controller Invoker Interface
 *
 * @package Slick\WebStack\Dispatcher
 */
interface ControllerInvokerInterface
{

    /**
     * Invokes the controller action returning view data
     *
     * @param ControllerInterface $controller
     * @param ControllerDispatch  $dispatch
     *
     * @return array
     */
    public function invoke(
        ControllerInterface $controller,
        ControllerDispatch $dispatch
    );
}