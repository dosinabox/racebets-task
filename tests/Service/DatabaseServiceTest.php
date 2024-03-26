<?php

namespace App\Tests\Service;

use App\Exception\NotFoundException;
use App\Service\DatabaseService;
use PHPUnit\Framework\TestCase;

final class DatabaseServiceTest extends TestCase
{
    private DatabaseService $databaseService;

    public function setUp(): void
    {
        $this->databaseService = new DatabaseService();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testDatabaseService(): void
    {
        //drop the test table (if exists)
        $this->databaseService->dropTable('tests');

        //create tests table
        $tableCreated = $this->databaseService->createTable('tests',
            [
                'firstName' => 'VARCHAR(255)',
                'lastName' => 'VARCHAR(255)'
            ]
        );
        self::assertTrue($tableCreated);

        //add new record to tests table
        $isAdded = $this->databaseService->addRowToTable('tests',
            [
                'firstName' => 'Slim',
                'lastName' => 'Shady',
            ]
        );
        self::assertTrue($isAdded);

        //find the newly created record by ID
        $found = $this->databaseService->findOneByID('tests', 1);
        self::assertEquals(
            [
                'id' => 1,
                'firstName' => 'Slim',
                'lastName' => 'Shady'
            ],
            $found
        );

        //update one record by ID
        $isUpdated = $this->databaseService->updateOneByID('tests', 1,
            [
                'firstName' => 'Marshall',
                'lastName' => 'Mathers'
            ]
        );
        self::assertTrue($isUpdated);

        //check if the record was updated
        $found = $this->databaseService->findOneByID('tests', 1);
        self::assertEquals(
            [
                'id' => 1,
                'firstName' => 'Marshall',
                'lastName' => 'Mathers'
            ],
            $found
        );

        //check that record does not exist
        $this->expectException(NotFoundException::class);
        $this->databaseService->findOneByID('tests', 99999);

        //drop the tests table
        $this->databaseService->dropTable('tests');
    }
}
