<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\UserInterface\Console;

use Slick\Http\Message\Server\Request;
use Slick\ModuleApi\Infrastructure\FrontController\MiddlewareHandlerInterface;
use Slick\ModuleApi\Infrastructure\FrontController\Position;
use Slick\WebStack\Infrastructure\FrontController\WebApplication;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * StackDisplayCommand
 *
 * @package Slick\WebStack\UserInterface\Console
 */
#[AsCommand(
    name: "app:http-stack",
    description: "Displays the list of the middlewares configured on the HTTP stack.",
    aliases: ["stack"]
)]
final class StackDisplayCommand extends Command
{
    /**
     * Executes the command.
     *
     * @param InputInterface $input The input object.
     * @param OutputInterface $output The output object.
     *
     * @return int The exit code of the command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $app = new WebApplication(new Request("GET", "/"), APP_ROOT);
        $app->loadModules();
        $app->loadMiddlewares();
        $list = $app->middlewareList();
        $table = $style->createTable();
        $table->setHeaderTitle("HTTP Stack Middlewares");
        $table->setHeaders(["Name", "Middleware", "Position"]);
        /** @var MiddlewareHandlerInterface $middleware */
        foreach ($list as $middleware) {
            $table->addRow([
                $middleware->name(),
                $this->parseMiddlewareClass($middleware),
                $this->parsePosition($middleware)
            ]);
        }
        $style->writeln('');
        $table->render();
        $style->write("  <info>Modules file: </info>{$app->rootPath()}".$app::MODULES_PATH."\n");
        return Command::SUCCESS;
    }

    private function parseMiddlewareClass(MiddlewareHandlerInterface $middleware): string
    {
        if (is_string($middleware->handler())) {
            return $middleware->handler();
        }

        if (is_callable($middleware->handler())) {
            return "--Callable--";
        }

        return get_class($middleware->handler());
    }

    private function parsePosition(MiddlewareHandlerInterface $middleware): string
    {
        $reference = $middleware->middlewarePosition()->reference();
        $refCheck = fn (?string $ref) => $ref ? " of $ref" : "";

        return match ($middleware->middlewarePosition()->position()->value) {
            Position::Top->value => "On top{$refCheck($reference)}",
            Position::Bottom->value => "On bottom{$refCheck($reference)}",
            Position::After->value => "After $reference",
            Position::Before->value => "Before $reference",
        };
    }
}
