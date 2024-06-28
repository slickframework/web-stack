<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure;

use Slick\WebStack\Infrastructure\Exception\InvalidModuleName;
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
    description: "Enable Slick modules.",
    aliases: ["enable"]
)]
final class EnableModuleCommand extends Command
{

    const ENABLED_MODULES_FILE = '/config/modules/enabled.php';
    private string $moduleListFile;

    public function __construct(private readonly string $appRoot)
    {
        parent::__construct();
        $this->moduleListFile = $this->appRoot . self::ENABLED_MODULES_FILE;
    }

    public function configure(): void
    {
        $this->addArgument('module', InputArgument::REQUIRED, "Module name to enable");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $moduleName = $input->getArgument('module');
        $this->checkConfigFile();
        $modules = require $this->moduleListFile;
        $modules[] = $this->retrieveModuleName($moduleName);
        file_put_contents($this->moduleListFile, "<?php\n\nreturn ['".implode("'\n'", $modules)."'];\n");
        $style = new SymfonyStyle($input, $output);
        $style->success("Module '{$moduleName}' enabled.");
        return Command::SUCCESS;
    }

    private function checkConfigFile(): void
    {
        $filename = $this->moduleListFile;
        if (!file_exists($filename)) {
            if (!is_dir($this->appRoot . '/config/modules')) {
                mkdir($this->appRoot . '/config/modules', 0755, true);
            }
            file_put_contents($filename, "<?php\n\nreturn [];\n");
        }
    }

    private function retrieveModuleName(string $moduleName): string
    {
        foreach ($this->possibleNames($moduleName) as $name) {
            if (class_exists($name)) {
                return $name;
            }
        }

        throw new InvalidModuleName(
            "Could not determine module name classname. Check SlickModuleInterface "
            ."implementation for module '$moduleName'"
        );
    }

    /**
     * Returns possible names for a module.
     *
     * @param string $module The name of the module.
     * @return array<string> An array containing possible names for the module.
     */
    private function possibleNames(string $module): array
    {
        $camelCaseModule = str_replace(' ', '', ucwords(str_replace('_', ' ', $module)));
        return [
            $module,
            "\\Slick\\WebStack\\{$camelCaseModule}Module",
            "\\Slick\\$camelCaseModule\\{$camelCaseModule}Module"
        ];
    }
}
