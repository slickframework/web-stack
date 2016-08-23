<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\MetaDataGenerator;

use Slick\Mvc\Console\MetaDataGeneratorInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * AbstractMetaDataGenerator
 *
 * @package Slick\Mvc\Console\MetaDataGenerator
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
abstract class AbstractMetaDataGenerator implements MetaDataGeneratorInterface
{

    /**
     * Used for input/output getters and setters
     */
    use ConsoleAwareMethods;
}