<?php

namespace App\Command;

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
        $table = $this->databaseService->createTable('users',
            [
                'email' => 'VARCHAR(255)',
                'firstName' => 'VARCHAR(255)',
                'lastName' => 'VARCHAR(255)',
                'gender' => 'VARCHAR(50)',
                'country' => 'VARCHAR(50)',
                'bonus' => 'DECIMAL(10, 2) DEFAULT 0',
                'money_real' => 'DECIMAL(10, 2) DEFAULT 0',
                'money_bonus' => 'DECIMAL(10, 2) DEFAULT 0'
            ]
        );

        $this->databaseService->addUniqueConstraint('users', 'email');

        if ($table) {
            return 'Users table created.';
        }

        return 'Users table not created.';
    }

    private function createTransactionsTable(): string
    {
        $table = $this->databaseService->createTable('transactions',
            [
                'date' => 'DATETIME DEFAULT CURRENT_TIMESTAMP',
                'type' => 'ENUM("deposit", "withdrawal") NOT NULL',
                'amount' => 'DECIMAL(10, 2) NOT NULL',
                'user_id' => 'INT'
            ]
        );

        $this->databaseService->addForeignKey('transactions', 'users', 'user_id');

        if ($table) {
            return 'Transactions table created.';
        }

        return 'Transactions table not created.';
    }
}
