<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Command\Task;

use Slick\WebStack\Console\Command\Task\AskForNamespace\ComposerReader;
use Slick\WebStack\Console\Command\TaskInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class AskForNamespace implements TaskInterface
{

    /**
     * @var Command
     */
    private $command;

    /**
     * @var ComposerReader
     */
    private $reader;

    /**
     * @var array
     */
    private $namespaces;

    /**
     * Creates an Ask For Namespace task
     *
     * @param Command        $command
     * @param ComposerReader $reader
     */
    public function __construct(Command $command, ComposerReader $reader)
    {
        $this->command = $command;
        $this->reader = $reader;
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
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var QuestionHelper $helper */
        $helper = $this->command->getHelper('question');

        $question = "Please select the namespace to use ({$this->namespaces()[0]}):";
        $question = new ChoiceQuestion(
            $question,
            $this->namespaces(),
            $this->namespaces()[0]
        );
        $namespace = $helper->ask($input, $output, $question);

        return $this->getNamespace($namespace);
    }

    /**
     * Get the list of namespaces as an array of strings
     *
     * @return array
     */
    private function namespaces()
    {
        if (!$this->namespaces) {
            $this->namespaces = $this->namespaceOptions();
        }
        return $this->namespaces;
    }

    /**
     * Converts the namespace collection to an array of strings
     *
     * @return array
     */
    private function namespaceOptions()
    {
        $data = [];
        foreach ($this->reader->nameSpaces() as $nameSpace) {
            $data[] = trim($nameSpace->getNameSpace(), '\\');
        }
        return $data;
    }

    /**
     * Get the selected namespace entity
     *
     * @param $selected
     * @return mixed|null|AskForNamespace\NameSpaceEntry
     */
    private function getNamespace($selected)
    {
        $nsEntity = null;
        foreach ($this->reader->nameSpaces() as $nameSpace) {
            if (trim($nameSpace->getNameSpace(), '\\') === $selected) {
                $nsEntity = $nameSpace;
                break;
            }
        }
        return $nsEntity;
    }
}
