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
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\Matcher\TraceableUrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

/**
 * RouterDebugMatchCommand
 *
 * @package Slick\WebStack\Infrastructure\Console
 */
#[AsCommand(name: 'router:match', description: 'Help debug routes by simulating a path info match')]
final class RouterDebugMatchCommand extends Command
{
    public function __construct(private RouterInterface $router)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputArgument('path_info', InputArgument::REQUIRED, 'A path info'),
                new InputOption('method', null, InputOption::VALUE_REQUIRED, 'Set the HTTP method'),
                new InputOption(
                    'scheme',
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Set the URI scheme (usually http or https)'
                ),
                new InputOption('host', null, InputOption::VALUE_REQUIRED, 'Set the URI host'),
            ])
            ->setHelp(<<<'EOF'
The <info>router:match</info> shows which routes match a given request and which don't and for what reason:

  <info>php router:match /foo</info>

or

  <info>php router:match/foo --method POST --scheme https --host symfony.com --verbose</info>

EOF
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);

        $matcher = new TraceableUrlMatcher($this->router->getRouteCollection(), $this->prepareContext($input));
        $traces = $matcher->getTraces($input->getArgument('path_info'));

        $style->newLine();

        $matches = false;
        foreach ($traces as $trace) {
            if (TraceableUrlMatcher::ROUTE_ALMOST_MATCHES == $trace['level']) {
                $style->text(
                    sprintf('Route <info>"%s"</> almost matches but %s', $trace['name'], lcfirst($trace['log']))
                );
            } elseif (TraceableUrlMatcher::ROUTE_MATCHES == $trace['level']) {
                $style->success(sprintf('Route "%s" matches', $trace['name']));

                $routerDebugCommand = $this->getApplication()?->find('debug:router');
                if (null !== $routerDebugCommand) {
                    $routerDebugCommand->run(new ArrayInput(['name' => $trace['name']]), $output);
                }

                $matches = true;
            } elseif ($input->getOption('verbose')) {
                $style->text(sprintf('Route "%s" does not match: %s', $trace['name'], $trace['log']));
            }
        }

        if (!$matches) {
            $style->error(sprintf('None of the routes match the path "%s"', $input->getArgument('path_info')));

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @param InputInterface $input
     * @return RequestContext
     */
    private function prepareContext(InputInterface $input): RequestContext
    {
        $context = $this->router->getContext();
        if (null !== $method = $input->getOption('method')) {
            $context->setMethod($method);
        }
        if (null !== $scheme = $input->getOption('scheme')) {
            $context->setScheme($scheme);
        }
        if (null !== $host = $input->getOption('host')) {
            $context->setHost($host);
        }
        return $context;
    }
}
