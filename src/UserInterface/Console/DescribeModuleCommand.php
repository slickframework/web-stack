<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\UserInterface\Console;

use Slick\WebStack\Infrastructure\Exception\InvalidModuleName;
use Slick\WebStack\Infrastructure\SlickModuleInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * DescribeModuleCommand
 *
 * @package Slick\WebStack\Infrastructure
 */
#[AsCommand(
    name: 'modules:describe',
    description: 'Explains the functionality of a module.',
    aliases: ['describe']
)]
final class DescribeModuleCommand extends Command
{

    use ModuleCommandTrait;

    protected string $appRoot;

    /** @var string  */
    protected string $moduleListFile;

    protected ?SymfonyStyle $outputStyle = null;

    public function __construct(string $appRoot)
    {
        parent::__construct();
        $this->appRoot = $appRoot;
        $this->moduleListFile = $this->appRoot . EnableModuleCommand::ENABLED_MODULES_FILE;
    }

    public function configure(): void
    {
        $this->addArgument('module', InputArgument::REQUIRED, "Module name to describe");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputStyle = new SymfonyStyle($input, $output);
        $moduleName = $input->getArgument('module');
        try {
            $retrieveModuleName = $this->retrieveModuleName($moduleName);
        } catch (InvalidModuleName $exception) {
            $this->outputStyle->writeln(
                "<error>{$exception->getMessage()}</error>"
            );
            return Command::FAILURE;
        }
        /** @var SlickModuleInterface $module */
        $module = new $retrieveModuleName();

        $this->renderTable([$module], $this->outputStyle);
        return Command::SUCCESS;
    }
}
