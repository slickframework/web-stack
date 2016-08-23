<?php

/**
 * This file is part of slick/mvc package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\Mvc\Console\MetaDataGenerator;

use Slick\Mvc\Console\MetaDataGeneratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Console Aware Methods
 *
 * @package Slick\Mvc\Console\MetaDataGenerator
 * @author  Filipe Silva <silva.filipe@gmail.com>
 */
trait ConsoleAwareMethods
{

    /**
     * @readwrite
     * @var InputInterface
     */
    protected $input;

    /**
     * @readwrite
     * @var OutputInterface
     */
    protected $output;

    /**
     * @readwrite
     * @var Command
     */
    protected $command;

    /**
     * Set the console input
     *
     * @param InputInterface $input
     *
     * @return self|MetaDataGeneratorInterface
     */
    public function setInput(InputInterface $input)
    {
        $this->input = $input;
        return $this;
    }

    /**
     * Get console input
     *
     * @return InputInterface
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Sets the console output
     *
     * @param OutputInterface $output
     *
     * @return self|MetaDataGeneratorInterface
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }

    /**
     * Get console output
     *
     * @return OutputInterface
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Gets command property
     *
     * @return Command
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * Sets command property
     *
     * @param Command $command
     *
     * @return self|MetaDataGeneratorInterface
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
        return $this;
    }


}