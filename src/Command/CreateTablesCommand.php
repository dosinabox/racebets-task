<?php

namespace App\Command;

use App\Service\DatabaseService;
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
            $output->writeln($this->createHistoryTable());
        } catch (\Throwable $throwable) {
            $output->writeln('Error on creating tables: ' . $throwable->getMessage());

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
                'email' => 'VARCHAR(120)',
                'firstName' => 'VARCHAR(120)',
                'lastName' => 'VARCHAR(120)',
                'gender' => 'VARCHAR(50)',
                'country' => 'VARCHAR(50)',
                'bonus' => 'INT',
                'money_real' => 'DECIMAL',
                'money_bonus' => 'DECIMAL'
            ]
        );

        $this->databaseService->addUniqueConstraint('users', 'email');

        if ($table) {
            return 'Users table created.';
        }

        return 'Users table not created.';
    }

    private function createHistoryTable(): string
    {
        $table = $this->databaseService->createTable('history',
            [
                'date' => 'DATETIME',
                'type' => 'VARCHAR(50)',
                'amount' => 'DECIMAL',
                'user_id' => 'INT'
            ]
        );

        if ($table) {
            return 'History table created.';
        }

        return 'History table not created.';
    }
}
