<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\Command;

use Slick\Mvc\Console\ConsoleAwareInterface;

/**
 * Task interface
 *
 * @package Slick\Mvc\Console\Command
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
interface TaskInterface extends ConsoleAwareInterface
{

    /**
     * Runs this task
     *
     * @return boolean
     */
    public function run();
}