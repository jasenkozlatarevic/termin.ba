
<?php

require_once __DIR__ . '/../config/Database.php';

abstract class BaseDao
{
    protected PDO $connection;
    protected string $table;

    public function __construct(string $table)
    {
        $this->connection = Database::connect();
        $this->table = $table;
    }

    public function getAll(): array
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM {$this->table}"
        );

        $statement->execute();

        return $statement->fetchAll();
    }

    public function getById(int $id): array|false
    {
        $statement = $this->connection->prepare(
            "SELECT * FROM {$this->table} WHERE id = :id"
        );

        $statement->execute(['id' => $id]);

        return $statement->fetch();
    }

    public function create(array $data): string
    {
        if (empty($data)) {
            throw new InvalidArgumentException(
                'Data for insertion cannot be empty.'
            );
        }

        $columns = array_keys($data);

        foreach ($columns as $column) {
            if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
                throw new InvalidArgumentException('Invalid column name.');
            }
        }

        $columnList = implode(', ', $columns);
        $placeholders = implode(
            ', ',
            array_map(fn($column) => ':' . $column, $columns)
        );

        $sql = "INSERT INTO {$this->table} ({$columnList})
                VALUES ({$placeholders})";

        $statement = $this->connection->prepare($sql);
        $statement->execute($data);

        return $this->connection->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if (empty($data)) {
            throw new InvalidArgumentException(
                'Data for update cannot be empty.'
            );
        }

        $columns = array_keys($data);

        foreach ($columns as $column) {
            if (
                $column === 'id' ||
                !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)
            ) {
                throw new InvalidArgumentException('Invalid column name.');
            }
        }

        $assignments = implode(
            ', ',
            array_map(fn($column) => "{$column} = :{$column}", $columns)
        );

        $sql = "UPDATE {$this->table}
                SET {$assignments}
                WHERE id = :_id";

        $data['_id'] = $id;

        $statement = $this->connection->prepare($sql);
        $statement->execute($data);

        return $statement->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $statement = $this->connection->prepare(
            "DELETE FROM {$this->table} WHERE id = :id"
        );

        $statement->execute(['id' => $id]);

        return $statement->rowCount() > 0;
    }
}
