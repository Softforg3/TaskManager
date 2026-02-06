<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Shared\Bus\CommandBusInterface;
use App\Application\User\Command\SyncUsers\SyncUsersCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

#[AsCommand(
    name: 'app:sync-users',
    description: 'Sync users from JSONPlaceholder API',
)]
final class SyncUsersConsoleCommand extends Command
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Syncing users from JSONPlaceholder API...');

        try {
            $this->commandBus->handle(new SyncUsersCommand());

            $io->success('Users synced successfully.');

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $io->error(sprintf('Failed to sync users: %s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}
