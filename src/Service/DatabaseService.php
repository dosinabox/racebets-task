<?php

namespace App\Service;

use App\Exception\DuplicateEntryException;
use App\Exception\NotFoundException;
use Exception;
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
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->exec('SET TRANSACTION ISOLATION LEVEL SERIALIZABLE');
    }

    public function createTable(string $tableName, array $columns): bool
    {
        $fields = 'id INT AUTO_INCREMENT';

        foreach ($columns as $column => $options) {
            $fields .= ',' . $column . ' ' . $options;
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

    public function addForeignKey(string $tableName, string $referenceTableName, string $foreignKey): bool
    {
        return $this->connection
            ->prepare("ALTER TABLE $tableName ADD FOREIGN KEY ($foreignKey) REFERENCES $referenceTableName(id)")
            ->execute();
    }

    /**
     * @param string $tableName
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function addRowToTable(string $tableName, array $data): bool
    {
        $values_prepared = [];

        array_walk($data, static function ($value) use (&$values_prepared) {
            $values_prepared[] = sprintf('"%s"', $value);
        });

        $values = implode(',', array_values($values_prepared));
        $columns = implode(',', array_keys($data));

        try {
            $isAdded = $this->connection
                ->prepare("INSERT INTO $tableName ($columns) VALUES ($values)")
                ->execute();
        } catch (Exception $exception) {
            throw $this->checkForDuplicateEntry($exception, $tableName);
        }

        return $isAdded;
    }

    public function findOneByID(string $tableName, int $id): false|array
    {
        $object = $this->connection
            ->query("SELECT * FROM $tableName WHERE id = $id LIMIT 1")
            ->fetch(PDO::FETCH_ASSOC);

        if (!$object) {
            throw new NotFoundException($id, $tableName);
        }

        return $object;
    }

    /**
     * @param string $tableName
     * @param int $id
     * @param array $data
     * @return bool
     * @throws Exception
     */
    public function updateOneByID(string $tableName, int $id, array $data): bool
    {
        $values_prepared = [];

        foreach ($data as $column => $value) {
            if (!is_null($value)) {
                $values_prepared[] = sprintf('%s = "%s"', $column, $value);
            }
        }

        $values = implode(',', $values_prepared);

        try {
            $isUpdated = $this->connection
                ->prepare("UPDATE $tableName SET $values WHERE id = $id")
                ->execute();
        } catch (Exception $exception) {
            throw $this->checkForDuplicateEntry($exception, $tableName);
        }

        return $isUpdated;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }

    //just because 23000 is not a valid HTTP response code
    private function checkForDuplicateEntry(Exception $exception, string $tableName): Exception
    {
        return $exception->getCode() === '23000' ? new DuplicateEntryException($tableName) : $exception;
    }
}
