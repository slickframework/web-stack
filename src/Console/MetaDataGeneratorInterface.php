<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console;
use Slick\Mvc\Console\MetaDataGenerator\ConsoleAwareMethods;


/**
 * Meta Data Generator Interface
 *
 * @package Slick\Mvc\Console
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
interface MetaDataGeneratorInterface extends ConsoleAwareInterface
{

    /**
     * Get generated data array
     *
     * @return array
     */
    public function getData();

}