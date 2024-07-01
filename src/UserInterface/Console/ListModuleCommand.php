<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\UserInterface\Console;

use Composer\Autoload\ClassLoader;
use Slick\WebStack\Infrastructure\SlickModuleInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ListModuleCommand
 *
 * @package Slick\WebStack\Infrastructure
 */
#[AsCommand(
    name: "modules:list",
    description: "Displays all available modules along with their enabled status.",
    aliases: ["modules"]
)]
final class ListModuleCommand extends Command
{

    use ModuleCommandTrait;

    protected string $appRoot;

    /** @var string  */
    protected string $moduleListFile;

    protected ?SymfonyStyle $outputStyle = null;

    public function __construct(string $appRoot, private readonly ClassLoader $classLoader)
    {
        parent::__construct();
        $this->appRoot = $appRoot;
        $this->moduleListFile = $this->appRoot . EnableModuleCommand::ENABLED_MODULES_FILE;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->outputStyle = new SymfonyStyle($input, $output);

        /** @var array<SlickModuleInterface> $existing */
        $existing = [];
        $classMap = $this->classLoader->getClassMap();
        foreach ($classMap as $className => $file) {
            $moduleClass = str_contains($file, 'Module.php') && !str_contains($file, 'AbstractModule.php');
            if (!$moduleClass) {
                continue;
            }

            $module = new $className();
            if ($module instanceof SlickModuleInterface) {
                $existing[] = $module;
            }
        }

        $this->renderTable($existing, $this->outputStyle);
        return Command::SUCCESS;
    }

    /**
     * Checks if a module does not exist in the given array of modules.
     *
     * @param string $moduleName The name of the module to check.
     * @param array<string> $modules The array of modules to check against.
     *
     * @return false|string Returns false if the module exists in the array, otherwise returns the module name.
     */
    protected function checkModuleNotExists(string $moduleName, array $modules): false|string
    {
        $retrieveModuleName = $this->retrieveModuleName($moduleName);
        return in_array(ltrim($retrieveModuleName, '\\'), $modules) ? false : $retrieveModuleName;
    }
}
