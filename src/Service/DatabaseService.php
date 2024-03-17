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

    public function createTable(string $tableName, array $columns): bool
    {
        $fields = 'id INT NOT NULL AUTO_INCREMENT';

        foreach ($columns as $column => $type) {
            $fields .= ',' . $column . ' ' . $type;
        }

        return $this->connection
            ->prepare("CREATE TABLE $tableName ($fields,PRIMARY KEY (id))")
            ->execute();
    }

    public function addUniqueConstraint(string $tableName, string $column): bool
    {
        return $this->connection
            ->prepare("ALTER TABLE $tableName ADD UNIQUE ($column)")
            ->execute();
    }

    public function addRowToTable(string $tableName, array $data): bool
    {
        $columns = implode(',', array_keys($data));

        $values_prepared = [];
        array_walk($data, static function ($value) use (&$values_prepared) {
            $values_prepared[] = sprintf('"%s"', $value);
        });
        $values = implode(',', array_values($values_prepared));

        return $this->connection
            ->prepare("INSERT INTO $tableName ($columns) VALUES ($values)")
            ->execute();
    }
}
