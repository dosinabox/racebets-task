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

    public function createTable(string $tableName, array $columns, ?string $primaryKey = null): bool
    {
        $fields = '';

        foreach ($columns as $column => $type) {
            $fields .= $column . ' ' . $type . ',';
        }

        if ($primaryKey && array_key_exists($primaryKey, $columns)) {
            $fields .= sprintf('PRIMARY KEY (%s)', $primaryKey);
        }

        return $this->connection
            ->prepare("CREATE TABLE $tableName ($fields)")
            ->execute();
    }
}
