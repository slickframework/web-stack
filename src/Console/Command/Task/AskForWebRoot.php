<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Command\Task;

use Slick\WebStack\Console\Command\TaskInterface;
use Slick\WebStack\Console\Exception\OperationAbortedException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Ask For Web Root
 *
 * @package Slick\WebStack\Console\Command\Task
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class AskForWebRoot implements TaskInterface
{
    /**
     * @var Command
     */
    private $command;

    /**
     * Creates an Ask For Web Root task
     *
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }


    /**
     * Executes the current task.
     *
     * This method can return the task execution result. For example if this
     * task is asking for user input it should return its input.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return mixed|false
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');
        $default = 'webroot';
        $question = "What's the application document root? ({$default}): ";
        $docRoot = $helper->ask(
            $input,
            $output,
            new Question($question, $default));

        return $this->check($docRoot, $input, $output) ? $docRoot : false;
    }

    /**
     * Checks if the document root has an index.php file. If so overwrite?
     *
     * @param string          $docRoot
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return bool
     */
    public function check($docRoot, InputInterface $input, OutputInterface $output)
    {
        if (!file_exists(getcwd()."/$docRoot")) return true;

        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        $question = "There is an 'index.php' file in this folder, overwrite it? (y/N): ";
        $default = false;
        $question = new ConfirmationQuestion($question, $default, '/(y|yes)/i');

        if (!$helper->ask($input, $output, $question)) {
            throw new OperationAbortedException(
                "Operation aborted! No file file will be overwritten."
            );
        }

        return true;
    }
}