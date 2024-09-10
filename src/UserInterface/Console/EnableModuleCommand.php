<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\UserInterface\Console;

use Slick\ModuleApi\Infrastructure\SlickModuleInterface;
use Slick\WebStack\Infrastructure\DependencyContainerFactory;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * EnableModuleCommand
 *
 * @package Slick\WebStack\Infrastructure
 */
#[AsCommand(
    name: "modules:enable",
    description: "Enables a modules.",
    aliases: ["enable"]
)]
class EnableModuleCommand extends Command
{
    use ModuleCommandTrait;

    /** @var string */
    public const ENABLED_MODULES_FILE = '/config/modules/enabled.php';

    protected string $appRoot;

    /** @var string  */
    protected string $moduleListFile;

    protected ?SymfonyStyle $outputStyle = null;

    /**
     * Creates a EnableModuleCommand
     *
     * @param string $appRoot
     */
    public function __construct(string $appRoot)
    {
        parent::__construct();
        $this->appRoot = $appRoot;
        $this->moduleListFile = $this->appRoot . self::ENABLED_MODULES_FILE;
    }

    public function configure(): void
    {
        $this->addArgument('module', InputArgument::REQUIRED, "Module name to enable");
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputStyle = new SymfonyStyle($input, $output);
        $moduleName = $input->getArgument('module');
        $this->checkConfigFile();

        $modules = $this->retrieveInstalledModules();
        if (!$retrieveModuleName = $this->checkModuleNotExists($moduleName, $modules)) {
            return Command::FAILURE;
        }

        $modules[] = $retrieveModuleName;
        file_put_contents($this->moduleListFile, $this->generateModuleConfig($modules));

        /** @var SlickModuleInterface $module */
        $module = new $retrieveModuleName();
        $module->onEnable(['container' => DependencyContainerFactory::instance()->container()]);

        $this->outputStyle?->writeln("<info>Module '$moduleName' enabled.</info>");
        return Command::SUCCESS;
    }
}
