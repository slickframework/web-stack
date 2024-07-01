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
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * ModuleCommandTrait
 *
 * @package Slick\WebStack\Infrastructure
 */
trait ModuleCommandTrait
{

    /**
     * Checks if a module does not exist in the given list of modules
     *
     * @param string $moduleName The name of the module to check
     * @param array<string> $modules The list of modules to check against
     * @return false|string Returns false if the module already exists, otherwise returns the modified module name
     */
    protected function checkModuleNotExists(string $moduleName, array $modules): false|string
    {
        try {
            $retrieveModuleName = $this->retrieveModuleName($moduleName);
            if (in_array(ltrim($retrieveModuleName, '\\'), $modules)) {
                $this->outputStyle?->writeln(
                    "<comment>The '$moduleName' module is already enabled. No changes have been made.</comment>"
                );
                return false;
            }
        } catch (InvalidModuleName $exception) {
            $this->outputStyle?->writeln(
                "<error>{$exception->getMessage()}</error>"
            );
            return false;
        }

        return $retrieveModuleName;
    }

    /**
     * Checks if the config file exists and creates it if it doesn't.
     *
     * @return void
     */
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

    /**
     * Generates a module configuration file contents.
     *
     * @param array<string> $modules The modules to include in the configuration file.
     *
     * @return string The generated module configuration file content.
     */
    protected function generateModuleConfig(array $modules): string
    {
        $fixed = [];
        foreach ($modules as $module) {
            $fixedModule = str_ends_with(ltrim($module, '\\'), '::class');
            $fixed[] = $fixedModule ? $module : "\\" . ltrim($module, '\\') . "::class";
        }

        return empty($fixed)
            ? "<?php\n\nreturn [];\n"
            : "<?php\n\nreturn [\n    " . implode(",\n    ", $fixed) . "\n];\n";
    }

    /**
     * Retrieves the module class name.
     *
     * @param string $moduleName The module name.
     *
     * @return class-string The module name.
     *
     * @throws InvalidModuleName If the module name classname cannot be determined.
     */
    protected function retrieveModuleName(string $moduleName): string
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
    protected function possibleNames(string $module): array
    {
        $camelCaseModule = str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $module)));
        $withoutSuffix = str_replace('Module', '', $camelCaseModule);
        return [
            $module,
            "\\Slick\\WebStack\\{$camelCaseModule}Module",
            "\\Slick\\$withoutSuffix\\{$camelCaseModule}Module",
            "\\Slick\\WebStack\\$camelCaseModule",
        ];
    }

    /**
     * Checks if a module exists in the given array of modules.
     *
     * @param string $moduleName The name of the module to check.
     * @param array<string> $modules The array of modules to search in.
     * @return false|string Returns the retrieved module name if it exists, otherwise returns false.
     */
    protected function checkModuleExists(string $moduleName, array $modules): false|string
    {
        try {
            $retrieveModuleName = $this->retrieveModuleName($moduleName);
            if (!in_array(ltrim($retrieveModuleName, '\\'), $modules)) {
                $this->outputStyle?->writeln(
                    "<comment>The '$moduleName' module is not enabled. No changes have been made.</comment>"
                );
                return false;
            }
        } catch (InvalidModuleName $exception) {
            $this->outputStyle?->writeln(
                "<error>{$exception->getMessage()}</error>"
            );
            return false;
        }

        return $retrieveModuleName;
    }

    /**
     * Renders a table of available modules.
     *
     * @param array<SlickModuleInterface> $modules The array of modules to display in the table.
     * @return void
     */
    protected function renderTable(array $modules, SymfonyStyle $style): void
    {
        $installedModules = file_exists($this->moduleListFile) ? require $this->moduleListFile : [];
        $table = $style->createTable();
        $table->setHeaderTitle("Available Modules");
        $table->setHeaders(["Name", "Description", "Enabled"]);

        foreach ($modules as $module) {
            $installed = "   <info>√</info>";
            $core = "   <fg=gray>-</>";
            $moduleName = $this->retrieveModuleName($module->name());
            $notInstalled = "   <fg=red>x</>";
            $status = $this->checkModuleNotExists($moduleName, $installedModules) ? $notInstalled : $installed;
            if (in_array($module->name(), ["console", "dispatcher", "front_controller"])) {
                $status = $core;
            }
            $table->addRow([$module->name(), $module->description(), $status]);
        }

        $table->render();
        $this->outputStyle?->write("  (<info>√</info>) enabled, ");
        $this->outputStyle?->write("(<fg=gray>-</>) core, ");
        $this->outputStyle?->write("(<fg=red>x</>) not enabled.\n");
    }
}
