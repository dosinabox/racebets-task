<?php

namespace App\Service;

use PDO;

class DatabaseService
{
    private PDO $connection;

    public function __construct()
    {
        $this->connection = new PDO(
            sprintf('%s:dbname=%s;host=%s',
                $_SERVER['DB_DRIVER'],
                $_SERVER['DB_NAME'],
                $_SERVER['DB_HOST']
            ),
            $_SERVER['DB_USER'],
            $_SERVER['DB_PASSWORD']
        );
    }

    public function createTable(string $tableName, array $columns): void
    {
        $fields = '';

        foreach ($columns as $column => $type) {
            $fields .= $column . ' ' . $type . ',';
        }
        $fields = rtrim($fields, ',');

        $this->connection->query(
            sprintf('CREATE TABLE %s (%s)',
                $tableName,
                $fields
            ),
        );
    }
}
