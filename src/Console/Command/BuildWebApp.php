<?php

/**
 * This file is part of slick/web_stack package
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Slick\WebStack\Console\Command;

use Slick\WebStack\Console\Command\Task\AskForNamespace;
use Slick\WebStack\Console\Command\Task\AskForWebRoot;
use Slick\WebStack\Console\Service\ContainerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * BuildWebApp
 *
 * @package Slick\WebStack\Console\Command
 * @author  Filipe Silva <silvam.filipe@gmail.com>
 */
class BuildWebApp extends Command
{

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('init')
            ->setDescription('Builds a basic, startup files and directory structure for a web application.')
        ;
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface  $input  An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = ContainerFactory::create($this)->container();
        $this->printBanner($output);
        $webRoot = $container->get(AskForWebRoot::class)
            ->execute($input, $output);
        $nameSpace = $container->get(AskForNamespace::class)
            ->execute($input, $output);
        return 0;
    }

    private function printBanner(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln('<info>Slick web application initialization</info>');
        $output->writeln('<info>------------------------------------</info>');
    }
}