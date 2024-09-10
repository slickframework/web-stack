<?php

/**
 * This file is part of web-stack
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Slick\WebStack\UserInterface\Console\Security;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slick\Di\ContainerInterface;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\Pbkdf2PasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\PhpPasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\Hasher\PlaintextPasswordHasher;
use Slick\WebStack\Domain\Security\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;
use UnhandledMatchError;

/**
 * HashPassword
 *
 * @package Slick\WebStack\Infrastructure\Console\Security
 */
#[AsCommand(
    name: 'security:hash-password',
    description: 'Hashes a given password using a one-way cryptographic algorithm',
    aliases: ["hash_pwd"]
)]
final class HashPassword extends Command
{

    public function __construct(private readonly ContainerInterface $container)
    {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this
            ->addArgument('plainPassword', InputArgument::REQUIRED, 'The password you need to hash')
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                "The algorithm type to use. Possible values 'bcrypt', 'pbkdf2', 'plain' or 'default'",
                'default'
            )
            ->addUsage('security:hash-password -t pbkdf2 "!AnExtremelyStrongPlainP4ssw0rd"')
        ;
    }


    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $style = new SymfonyStyle($input, $output);
        $inputPassword = $input->getArgument('plainPassword');
        try {
            $passwordHasher = $this->retrieveHasher($input);
            $hash = $passwordHasher->hash($inputPassword);
            $this->renderInfo($style, $inputPassword, $hash, $passwordHasher);
        } catch (UnhandledMatchError) {
            $style->error('Unknown algorithm type');
            return Command::INVALID;
        } catch (Throwable $e) {
            $style->error($e->getMessage());
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    private function retrieveHasher(InputInterface $input): PasswordHasherInterface
    {
        $option = (string) $input->getOption('type');
        return match ($option) {
            "pbkdf2" => $this->container->get(Pbkdf2PasswordHasher::class),
            "plain" => $this->container->get(PlaintextPasswordHasher::class),
            "bcrypt" => $this->container->get(PhpPasswordHasher::class),
            "default" => $this->container->get(PasswordHasherInterface::class),
            default => throw new UnhandledMatchError()
        };
    }

    private function renderInfo(
        SymfonyStyle $style,
        string $inputPassword,
        string $hash,
        PasswordHasherInterface $passwordHasher
    ): void {
        $options = '';
        foreach ($passwordHasher->info() as $name => $value) {
            $options .= sprintf("<info>%s</info>: %s\n", $name, $value);
        }
        $table = $style->createTable();
        $table->setHeaders(['Password', 'Hash', 'Hasher']);
        $table->setHeaderTitle("Password hash utility");
        $table->addRow([$inputPassword, $hash, trim($options)]);
        $table->render();
    }
}
