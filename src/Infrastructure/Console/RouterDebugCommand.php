<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\Infrastructure\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * RouterDebugCommand
 *
 * @package Slick\WebStack\Infrastructure\Console
 */
#[AsCommand(name: 'debug:router', description: 'Display current routes for an application')]
final class RouterDebugCommand extends Command
{

    public function __construct(private RouterInterface $router)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('name', InputArgument::OPTIONAL, 'A route name'),
            ])
            ->setHelp("Displays configured routes.")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');
        $routes = $this->router->getRouteCollection();

        if ($name) {
            $route = $routes->get($name);
            $this->renderRouteTable($route, $style, $name);
            return Command::SUCCESS;
        }

        $this->renderListTable($routes, $style);

        return Command::SUCCESS;
    }

    private function renderListTable(RouteCollection $routes, SymfonyStyle $style): void
    {
        $table = $style->createTable();

        $table->setHeaderTitle("Configured routes");
        $headers = ['Name', 'Methods', 'Host', 'Path', 'Controller'];
        $table->setHeaders($headers);
        foreach ($routes as $name => $route) {
            $row = [
                $name,
                empty($route->getMethods()) ? "ANY" : implode(", ", $route->getMethods()),
                strlen($route->getHost()) > 0 ? $route->getHost() : "ANY",
                $route->getPath(),
                $route->getDefault('_controller')."::".$route->getDefault('_action')."()"
            ];

            $table->addRow($row);
        }

        $style->writeln('');
        $table->render();
    }

    private function renderRouteTable(?Route $route, SymfonyStyle $style, string $name): void
    {
        if (!$route) {
            $style->warning("There's no route named: '$name'.");
            return;
        }

        $table = $style->createTable();

        $table->setHeaderTitle("Found route");
        $headers = ['Property', 'Value'];
        $table->setHeaders($headers);
        $options = [];
        foreach ($route->getOptions() as $key => $value) {
            $options[] = [$key, $value];
        }
        $table->addRows([
            ["Route name", $name],
            ["Methods", empty($route->getMethods()) ? "ANY" : implode(", ", $route->getMethods())],
            ["Host", strlen($route->getHost()) > 0 ? $route->getHost() : "ANY"],
            ["Path", $route->getPath()],
            ["Controller", $route->getDefault('_controller')."::".$route->getDefault('_action')."()"],
            ...$options
        ]);

        $style->writeln('');
        $table->render();
    }
}
