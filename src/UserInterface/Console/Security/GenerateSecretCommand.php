<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\UserInterface\Console\Security;

use Random\RandomException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * GenerateSecretCommand
 *
 * @package Slick\WebStack\UserInterface\Console\Security
 */
#[AsCommand(
    name: 'security:generate-secret',
    description: 'Generate a secure encryption secret using a cryptographically secure method',
    aliases: ["secretgen"]
)]
final class GenerateSecretCommand extends Command
{

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->addArgument('size', InputArgument::OPTIONAL, 'Length in bytes of the generated secret', 32)
            ->addOption(
                'encoding',
                'e',
                InputOption::VALUE_OPTIONAL,
                'Encoding of the generated secret. Possible values are: hex (hexadecimal), base64. Defaults to hex',
                'base64'
            )
            ->addUsage("security:generate-secret 24 -e base64")
        ;
    }

    /**
     * @throws RandomException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $encoding = $input->getOption('encoding');
        $length = $this->size($input);
        $secret = random_bytes($length);

        $secret = match ($encoding) {
            'base64' => base64_encode($secret),
            default => bin2hex($secret),
        };

        $style = new SymfonyStyle($input, $output);

        $style->writeln('');

        $table = $style->createTable();
        $table->setHeaders(['Generated secret', 'Size']);
        $table->setHeaderTitle("Generate secret utility");
        $table->addRow([$secret, $length]);
        $table->render();

        return Command::SUCCESS;
    }

    /**
     * @return int<1, max>
     */
    private function size(InputInterface $input): int
    {
        $size = (int) $input->getArgument('size');
        if ($size > 0) {
            return $size;
        }
        return 1;
    }
}
