<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\User\Command\SyncUsersCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[AsCommand(
    name: 'app:sync-users',
    description: 'Sync users from JSONPlaceholder API',
)]
class SyncUsersConsoleCommand extends Command
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->info('Syncing users from JSONPlaceholder API...');

        try {
            $envelope = $this->messageBus->dispatch(new SyncUsersCommand());
            $handledStamp = $envelope->last(HandledStamp::class);
            $synced = $handledStamp?->getResult() ?? 0;

            $io->success(sprintf('Successfully synced %d users.', $synced));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $io->error(sprintf('Failed to sync users: %s', $e->getMessage()));

            return Command::FAILURE;
        }
    }
}
