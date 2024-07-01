<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\UserInterface\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * DisableModuleCommand
 *
 * @package Slick\WebStack\Infrastructure
 */
#[AsCommand(
    name: "modules:disable",
    description: "Disables a module",
    aliases: ["disable"],
)]
final class DisableModuleCommand extends DescribeModuleCommand
{

    use ModuleCommandTrait;


    public function configure(): void
    {
        $this->addArgument('module', InputArgument::REQUIRED, 'Module name to disable');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputStyle = new SymfonyStyle($input, $output);
        $moduleName = $input->getArgument('module');

        if (!file_exists($this->moduleListFile)) {
            $this->outputStyle->writeln(
                "<comment>Module '$moduleName' is not installed. No change was made.</comment>"
            );
            return Command::FAILURE;
        }

        $modules = $this->retrieveInstalledModules();
        if (!$className = $this->checkModuleExists($moduleName, $modules)) {
            return Command::FAILURE;
        }

        $new = [];
        foreach ($modules as $module) {
            if (str_ends_with($className, $module)) {
                continue;
            }
            $new[] = $module;
        }

        file_put_contents($this->moduleListFile, $this->generateModuleConfig($new));
        $this->outputStyle->writeln("<info>Module '$moduleName' disabled.</info>");
        return Command::SUCCESS;
    }
}
