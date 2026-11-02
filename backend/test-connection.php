
<?php

require_once __DIR__ . '/config/Database.php';

try {
    $connection = Database::connect();

    echo "Database connection successful!" . PHP_EOL;

    $statement = $connection->query('SELECT DATABASE() AS database_name');
    $result = $statement->fetch();

    echo "Connected database: " . $result['database_name'] . PHP_EOL;
} catch (PDOException $exception) {
    echo "Database connection failed: " . $exception->getMessage() . PHP_EOL;
}
