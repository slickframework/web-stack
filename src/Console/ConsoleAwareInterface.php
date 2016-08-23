<?php

/**
 * This file is part of slick/vc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console Aware Interface
 *
 * @package Slick\Mvc\Console
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
interface ConsoleAwareInterface
{

    /**
     * Set the console input
     *
     * @param InputInterface $input
     *
     * @return MetaDataGeneratorInterface
     */
    public function setInput(InputInterface $input);

    /**
     * Sets the console output
     *
     * @param OutputInterface $output
     *
     * @return MetaDataGeneratorInterface
     */
    public function setOutput(OutputInterface $output);

    /**
     * Set console command
     *
     * @param Command $command
     *
     * @return MetaDataGeneratorInterface
     */
    public function setCommand(Command $command);
}