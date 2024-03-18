<?php

namespace App\Command;

use App\Entity\Transaction;
use App\Entity\User;
use App\Service\DatabaseService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-tables')]
class CreateTablesCommand extends Command
{
    public function __construct(private readonly DatabaseService $databaseService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln($this->createUsersTable());
            $output->writeln($this->createTransactionsTable());
        } catch (Exception $exception) {
            $output->writeln('Error on creating tables: ' . $exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setDescription('Creates tables in database.');
    }

    private function createUsersTable(): string
    {
        $table = $this->databaseService->createTable(User::TABLE, User::SCHEMA);
        $this->databaseService->addUniqueConstraint(User::TABLE, User::COLUMN_EMAIL);

        if ($table) {
            return 'Users table created.';
        }

        return 'Users table not created.';
    }

    private function createTransactionsTable(): string
    {
        $table = $this->databaseService->createTable(Transaction::TABLE, Transaction::SCHEMA);
        $this->databaseService->addForeignKey(Transaction::TABLE, User::TABLE, Transaction::COLUMN_USER_ID);

        if ($table) {
            return 'Transactions table created.';
        }

        return 'Transactions table not created.';
    }
}
